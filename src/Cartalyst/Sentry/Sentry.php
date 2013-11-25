<?php namespace Cartalyst\Sentry;
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Activations\IlluminateActivationRepository;
use Cartalyst\Sentry\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentry\Checkpoints\CheckpointInterface;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Persistence\PersistenceInterface;
use Cartalyst\Sentry\Users\IlluminateUserRepository;
use Cartalyst\Sentry\Users\UserRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;
use Closure;
use Illuminate\Events\Dispatcher;

class Sentry {

	/**
	 * The current cached, logged in user.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * The persistence driver (the class which actually manages sessions).
	 *
	 * @var \Cartalyst\Sentry\Persistence\PersistenceInterface
	 */
	protected $persistence;

	/**
	 * User repository.
	 *
	 * @var \Cartalyst\Sentry\Users\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * Event dispatcher.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Flag for whether checkpoints are enabled.
	 *
	 * @var bool
	 */
	protected $checkpoints = array();

	/**
	 * Activations repository.
	 *
	 * @var \Cartalyst\Sentry\Activations\ActivationRepositoryInterface
	 */
	protected $activations;

	/**
	 * Create a new Sentry instance.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistenceInterface  $persistence
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $users
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 */
	public function __construct(PersistenceInterface $persistence, UserRepositoryInterface $users = null, Dispatcher $dispatcher = null)
	{
		$this->persistence = $persistence;

		if (isset($users))
		{
			$this->users = $users;
		}

		if (isset($dispatcher))
		{
			$this->dispatcher = $dispatcher;
		}
	}

	/**
	 * Registers a user. You may provide a callback to occur before the user
	 * is saved, or provide a true boolean as a shortcut to activation.
	 *
	 * @param  array  $credentials
	 * @param  \Closure|bool  $callback
	 * @return \Cartalyst\Sentry\Users\UserInteface|bool
	 */
	public function register(array $credentials, $callback = null)
	{
		$valid = $this->users->validForCreation($credentials);

		if ($callback !== null and ! $callback instanceof Closure and $callback !== true)
		{
			throw new \InvalidArgumentException('You must provide a closure or true boolean.');
		}

		$argument = ($callback instanceof Closure) ? $callback : null;

		$user = $this->users->create($credentials, $argument);

		if ($callback === true)
		{
			$this->activate($user);
		}

		return $user;
	}

	/**
	 * Registers a user, activating them.
	 *
	 * @param  array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInteface|bool
	 */
	public function registerAndActivate(array $credentials)
	{
		return $this->register($credentials, true);
	}

	/**
	 * Activates the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function activate($user)
	{
		if (is_string($user))
		{
			$users = $this->getUserRepository();

			$user = $users->findById($user);
		}

		if ( ! $user instanceof UserInterface)
		{
			throw new \InvalidArgumentException('No valid user was provided.');
		}

		$activations = $this->getActivationsRepository();

		$code = $activations->create($user);
		return $activations->complete($user, $code);
	}

	/**
	 * Checks to see if a user is logged in.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function check()
	{
		$code = $this->persistence->check();

		if ($code === null)
		{
			return false;
		}

		$user = $this->users->findByPersistenceCode($code);

		if ($user === null)
		{
			return false;
		}

		if ( ! $this->cycleCheckpoints('check', $user))
		{
			return false;
		}

		return $this->user = $user;
	}

	/**
	 * Checks to see if a user is logged in, bypassing checkpoints
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceCheck()
	{
		return $this->bypassCheckpoints(function($sentry)
		{
			return $sentry->check();
		});
	}

	/**
	 * Returns if we are currently a guest.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function guest()
	{
		return ( ! $this->check());
	}

	/**
	 * Authenticates a user, with "remember" flag.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface|array  $credentials
	 * @param  bool  $remember
	 * @param  bool  $bool
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function authenticate($credentials, $remember = false, $login = true)
	{
		if ($credentials instanceof UserInterface)
		{
			$user = $credentials;
		}
		else
		{
			$user = $this->users->findByCredentials($credentials);
			$valid = $this->users->validateCredentials($user, $credentials);

			if ($user === null or $valid === false)
			{
				$this->cycleCheckpoints('fail', $user, false);

				return false;
			}
		}

		if ( ! $this->cycleCheckpoints('login', $user))
		{
			return false;
		}

		if ($login === false)
		{
			return true;
		}

		$method = ($remember === true) ? 'loginAndRemember' : 'login';

		return $this->$method($user);
	}

	/**
	 * Authenticates a user, with "remember" flag.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface|array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function authenticateAndRemember($credentials)
	{
		return $this->authenticate($credentials, true);
	}

	/**
	 * Forces an authentication to bypass checkpoints.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface|array  $credentials
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceAuthenticate($credentials, $remember = false)
	{
		return $this->bypassCheckpoints(function($sentry) use ($credentials, $remember)
		{
			return $sentry->authenticate($credentials, $remember);
		});
	}

	/**
	 * Forces an authentication to bypass checkpoints, with "remember" flag.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface|array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceAuthenticateAndRemember($credentials)
	{
		return $this->forceAuthenticate($credentials, true);
	}

	/**
	 * Attempt a stateless authentication.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface|array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function stateless($credentials)
	{
		return $this->authenticate($credentials, false, false);
	}

	/**
	 * Persists a login for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function login(UserInterface $user, $remember = false)
	{
		$method = ($remember === true) ? 'addAndRemember' : 'add';

		return $this->persistence->$method($user);
	}

	/**
	 * Persists a login for the given user, with "remember" flag.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function loginAndRemember(UserInterface $user)
	{
		return $this->login($user, true);
	}

	/**
	 * Log the current (or given) user out.
	 *
	 * @param  bool  $everywhere
	 * @return bool
	 */
	public function logout($everywhere = false)
	{
		if ($this->user === null)
		{
			return true;
		}

		$method = ($everywhere === true) ? 'flush' : 'remove';

		return $this->persistence->$method($this->user);
	}

	/**
	 * Pass a closure to Sentry to bypass checkpoints.
	 *
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function bypassCheckpoints(Closure $callback)
	{
		// Temporarily rmeove the array of registered checkpoints
		$checkpoints = $this->checkpoints;
		$this->checkpoints = false;

		// Fire the callback
		$result = $callback($this);

		// Reset checkpoints
		$this->checkpoints = $checkpoints;

		return $result;
	}

	/**
	 * Returns if checkpoints are enabled.
	 *
	 * @return bool
	 */
	public function checkpointsEnabled()
	{
		return $this->checkpoints;
	}

	/**
	 * Enables checkpoints.
	 *
	 * @return void
	 */
	public function enableCheckpoints()
	{
		$this->checkpoints = true;
	}

	/**
	 * Disables checkpoints.
	 *
	 * @return void
	 */
	public function disableCheckpoints()
	{
		$this->checkpoints = false;
	}

	/**
	 * Add a new checkpoint to Sentry.
	 *
	 * @param  \Cartalyst\Sentry\Checkpoints\CheckpointInterface  $checkpoint
	 * @return void
	 */
	public function addCheckpoint(CheckpointInterface $checkpoint)
	{
		$this->checkpoints[] = $checkpoint;
	}

	/**
	 * Cycles through all registered checkpoints for a user. Checkpoints may
	 * throw their own exceptions, however, if just one returns false, the
	 * cycle fails.
	 *
	 * @param  string  $method
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $halt
	 * @return bool
	 */
	protected function cycleCheckpoints($method, UserInterface $user = null, $halt = true)
	{
		foreach ($this->checkpoints as $checkpoint)
		{
			$response = $checkpoint->$method($user);

			if ($response === false and $halt === true)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Register a Sentry event.
	 *
	 * @param  string  $event
	 * @param  \Closure|string  $callback
	 * @param  int  $priority
	 * @return void
	 */
	protected function registerEvent($event, $callback, $priority = 0)
	{
		$dispatcher = $this->getEventDispatcher();

		$dispatcher->listen("sentry.{$event}", $callback, $priority);
	}

	/**
	 * Call a Sentry event.
	 *
	 * @param  string  $event
	 * @param  mixed   $payload
	 * @return mixed
	 */
	protected function fireEvent($event, $payload = array())
	{
		$dispatcher = $this->getEventDispatcher();

		return $dispatcher->fire("sentry.{$event}", $payload);
	}

	/**
	 * Get the currently logged in user, lazily checking for it.
	 *
	 * @param  bool  $check
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUser($check = true)
	{
		if ($check === true and $this->user === null)
		{
			$this->check();
		}

		return $this->user;
	}

	/**
	 * Get the currently logged in user, lazily checking for it.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUserWithoutCheck()
	{
		return $this->getUser(false);
	}

	/**
	 * Set the user associated with Sentry (does not log in).
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Get the persistence instance.
	 *
	 * @return \Cartalyst\Sentry\Persistence\PersistenceInterface
	 */
	public function getPersistence()
	{
		return $this->persistence;
	}

	/**
	 * Set the persistence instance.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistenceInterface
	 * @return void
	 */
	public function setPersistence(PersistenceInterface $persistence)
	{
		$this->persistence = $persistence;
	}

	/**
	 * Get the user repository.
	 *
	 * @return \Cartalyst\Sentry\Users\UserRepositoryInterface
	 */
	public function getUserRepository()
	{
		if ($this->users === null)
		{
			$this->users = $this->createUserRepository();
		}

		return $this->users;
	}

	/**
	 * Set the user repository.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $users
	 * @return void
	 */
	public function setUserRepository(UserRepositoryInterface $users)
	{
		$this->users = $users;
	}

	/**
	 * Creates a default user repository if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Users\IlluminateUserRepository
	 */
	protected function createUserRepository()
	{
		$hasher = new NativeHasher;
		$model = 'Cartalyst\Sentry\Users\EloquentUser';

		return new IlluminateUserRepository($hasher, $model);
	}

	/**
	 * Get the event dispatcher.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public function getEventDispatcher()
	{
		if ($this->dispatcher === null)
		{
			$this->dispatcher = $this->createEventDispatcher();
		}

		return $this->dispatcher;
	}

	/**
	 * Set the event dispatcher.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function setEventDispatcher(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Creates a default event dispatcher if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Users\IlluminateEventDispatcher
	 */
	protected function createEventDispatcher()
	{
		return new Dispatcher;
	}

	/**
	 * Get the activations repository.
	 *
	 * @return \Cartalyst\Sentry\Activationss\ActivationRepositoryInterface
	 */
	public function getActivationsRepository()
	{
		if ($this->activations === null)
		{
			$this->activations = $this->createActivationsRepository();
		}

		return $this->activations;
	}

	/**
	 * Set the activations repository.
	 *
	 * @param  \Cartalyst\Sentry\Activationss\ActivationRepositoryInterface  $activations
	 * @return void
	 */
	public function setActivationsRepository(ActivationRepositoryInterface $activations)
	{
		$this->activations = $activations;
	}

	/**
	 * Creates a default activations repository if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Activationss\IlluminateActivationRepository
	 */
	protected function createActivationsRepository()
	{
		$model = 'Cartalyst\Sentry\Activationss\EloquentActivation';

		return new IlluminateActivationRepository($model);
	}

	/**
	 * Dynamically pass missing methods to Sentry.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (starts_with($method, 'findBy'))
		{
			$users = $this->getUserRepository();

			return call_user_func_array(array($users, $method), $parameters);
		}

		$className = get_class($this);

		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}
