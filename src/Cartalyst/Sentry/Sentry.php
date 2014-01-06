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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Activations\IlluminateActivationRepository;
use Cartalyst\Sentry\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentry\Checkpoints\CheckpointInterface;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Groups\IlluminateGroupRepository;
use Cartalyst\Sentry\Groups\GroupRepositoryInterface;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Persistence\PersistenceInterface;
use Cartalyst\Sentry\Persistence\SentryPersistence;
use Cartalyst\Sentry\Reminders\IlluminateReminderRepository;
use Cartalyst\Sentry\Reminders\ReminderRepositoryInterface;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Users\IlluminateUserRepository;
use Cartalyst\Sentry\Users\UserRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;
use Closure;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Request;

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
	 * Cached, available methods on the user repository, used for dynamic calls.
	 *
	 * @var array
	 */
	protected $userMethods = array();

	/**
	 * Group repository.
	 *
	 * @var \Cartalyst\Sentry\Groups\GroupRepositoryInterface
	 */
	protected $groups;

	/**
	 * Event dispatcher.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Array that holds all the enabled checkpoints.
	 *
	 * @var array
	 */
	protected $checkpoints = array();

	/**
	 * Activations repository.
	 *
	 * @var \Cartalyst\Sentry\Activations\ActivationRepositoryInterface
	 */
	protected $activations;

	/**
	 * Reminders repository.
	 *
	 * @var \Cartalyst\Sentry\Reminders\ReminderRepositoryInterface
	 */
	protected $reminders;

	/**
	 * The closure to retrieve request credentials.
	 *
	 * @var \Closure
	 */
	protected $requestCredentials;

	/**
	 * The closure used to create a basic response for failed HTTP auth.
	 *
	 * @var \Closure
	 */
	protected $basicResponse;

	/**
	 * Create a new Sentry instance.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistenceInterface  $persistence
	 * @param  \Cartalyst\Sentry\Groups\GroupRepositoryInterface  $groups
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $users
	 */
	public function __construct(PersistenceInterface $persistence = null, UserRepositoryInterface $users = null, GroupRepositoryInterface $groups = null)
	{
		if (isset($persistence))
		{
			$this->persistence = $persistence;
		}

		if (isset($users))
		{
			$this->users = $users;
		}

		if (isset($groups))
		{
			$this->groups = $groups;
		}
	}

	/**
	 * Registers a user. You may provide a callback to occur before the user
	 * is saved, or provide a true boolean as a shortcut to activation.
	 *
	 * @param  array  $credentials
	 * @param  \Closure|bool  $callback
	 * @return \Cartalyst\Sentry\Users\UserInteface|bool
	 * @throws \InvalidArgumentException
	 */
	public function register(array $credentials, $callback = null)
	{
		if ($callback !== null and ! $callback instanceof Closure and ! is_bool($callback))
		{
			throw new \InvalidArgumentException('You must provide a closure or a boolean.');
		}

		$valid = $this->users->validForCreation($credentials);

		if ($valid === false)
		{
			return false;
		}

		$argument = $callback instanceof Closure ? $callback : null;

		$user = $this->users->create($credentials, $argument);

		if ($callback === true)
		{
			$this->activate($user);
		}

		return $user;
	}

	/**
	 * Registers and activates the user.
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
	 * @throws \InvalidArgumentException
	 */
	public function activate($user)
	{
		if (is_string($user))
		{
			$users = $this->getUserRepository();

			$user = $users->findById($user);
		}
		elseif (is_array($user))
		{
			$users = $this->getUserRepository();

			$user = $users->findByCredentials($user);
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
		return ! $this->check();
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

			$valid = $user !== null ? $this->users->validateCredentials($user, $credentials) : false;

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

		$method = $remember === true ? 'loginAndRemember' : 'login';

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
	 * Forces an authentication to bypass checkpoints, with the "remember" flag.
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
	 * Attempt to authenticate using HTTP Basic Auth.
	 *
	 * @return mixed
	 */
	public function basic()
	{
		$credentials = $this->getRequestCredentials();

		// We don't really want to add a throttling record for the
		// first failed login attempt, which actually occurs when
		// the user first hits a protected route.
		if ($credentials === null)
		{
			return $this->getBasicResponse();
		}

		$user = $this->stateless($credentials);

		if ($user) return;

		return $this->getBasicResponse();
	}

	/**
	 * Get the request credentials.
	 *
	 * @return array
	 */
	public function getRequestCredentials()
	{
		if ($this->requestCredentials === null)
		{
			$this->requestCredentials = function()
			{
				$credentials = array();

				if (isset($_SERVER['PHP_AUTH_USER']))
				{
					$credentials['login'] = $_SERVER['PHP_AUTH_USER'];
				}

				if (isset($_SERVER['PHP_AUTH_PW']))
				{
					$credentials['password'] = $_SERVER['PHP_AUTH_PW'];
				}

				if (count($credentials) > 0)
				{
					return $credentials;
				}
			};
		}

		$credentials = $this->requestCredentials;
		return $credentials();
	}

	/**
	 * Set the closure which resolves request credentials.
	 *
	 * @param  \Closure  $requestCredentials
	 * @return void
	 */
	public function setRequestCredentials(Closure $requestCredentials)
	{
		$this->requestCredentials = $requestCredentials;
	}

	/**
	 * Sends a response when HTTP basic authentication fails.
	 *
	 * @return mixed
	 */
	public function getBasicResponse()
	{
		// Default the basic response
		if ($this->basicResponse === null)
		{
			$this->basicResponse = function()
			{
				if (headers_sent())
				{
					throw new \RuntimeException('Attempting basic auth after headers have already been sent.');
				}

				header('WWW-Authenticate: Basic');
				header('HTTP/1.0 401 Unauthorized');

				echo 'Invalid credentials.';
				exit;
			};
		}

		$response = $this->basicResponse;
		return $response();
	}

	/**
	 * Set the callback which creates a basic response.
	 *
	 * @param  \Closure  $basicResonse
	 * @return void
	 */
	public function creatingBasicResponse(Closure $basicResponse)
	{
		$this->basicResponse = $basicResponse;
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
		$method = $remember === true ? 'addAndRemember' : 'add';
		$this->persistence->$method($user);

		return $this->users->recordLogin($user);
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
		$user = $this->getUser();

		if ($user === null)
		{
			return true;
		}

		$method = $everywhere === true ? 'flush' : 'remove';
		$this->persistence->$method($user);

		return $this->users->recordLogout($user);
	}

	/**
	 * Pass a closure to Sentry to bypass checkpoints.
	 *
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function bypassCheckpoints(Closure $callback)
	{
		// Temporarily remove the array of registered checkpoints
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
	 * Cycles through all the registered checkpoints for a user. Checkpoints
	 * may throw their own exceptions, however, if just one returns false,
	 * the cycle fails.
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
	 * Set the user associated with Sentry (does not log in).
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
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
		if ($this->persistence === null)
		{
			$this->persistence = $this->createPersistence();
		}

		return $this->persistence;
	}

	/**
	 * Set the persistence instance.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistenceInterface  $persistence
	 * @return void
	 */
	public function setPersistence(PersistenceInterface $persistence)
	{
		$this->persistence = $persistence;
	}

	/**
	 * Creates a persistence instance.
	 *
	 * @return \Cartalyst\Sentry\Users\IlluminateUserRepository
	 */
	protected function createPersistence()
	{
		$session = new NativeSession;
		$cookie = new NativeCookie;

		return new SentryPersistence($session, $cookie);
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
			$this->userMethods = array();
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
		$this->userMethods = array();
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
	 * Get the group repository.
	 *
	 * @return \Cartalyst\Sentry\Groups\GroupRepositoryInterface
	 */
	public function getGroupRepository()
	{
		if ($this->groups === null)
		{
			$this->groups = $this->createGroupRepository();
		}

		return $this->groups;
	}

	/**
	 * Set the group repository.
	 *
	 * @param  \Cartalyst\Sentry\Groups\GroupRepositoryInterface  $groups
	 * @return void
	 */
	public function setGroupRepository(GroupRepositoryInterface $groups)
	{
		$this->groups = $groups;
	}

	/**
	 * Creates a default group repository if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Groups\IlluminateGroupRepository
	 */
	protected function createGroupRepository()
	{
		$model = 'Cartalyst\Sentry\Groups\EloquentGroup';

		return new IlluminateGroupRepository($model);
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
	 * @return \Cartalyst\Sentry\Activations\ActivationRepositoryInterface
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
	 * @param  \Cartalyst\Sentry\Activations\ActivationRepositoryInterface  $activations
	 * @return void
	 */
	public function setActivationsRepository(ActivationRepositoryInterface $activations)
	{
		$this->activations = $activations;
	}

	/**
	 * Creates a default activations repository if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Activations\IlluminateActivationRepository
	 */
	protected function createActivationsRepository()
	{
		$model = 'Cartalyst\Sentry\Activations\EloquentActivation';

		return new IlluminateActivationRepository($model);
	}

	/**
	 * Get the reminders repository.
	 *
	 * @return \Cartalyst\Sentry\Reminders\ReminderRepositoryInterface
	 */
	public function getRemindersRepository()
	{
		if ($this->reminders === null)
		{
			$this->reminders = $this->createRemindersRepository();
		}

		return $this->reminders;
	}

	/**
	 * Set the reminders repository.
	 *
	 * @param  \Cartalyst\Sentry\Reminders\ReminderRepositoryInterface  $reminders
	 * @return void
	 */
	public function setRemindersRepository(ReminderRepositoryInterface $reminders)
	{
		$this->reminders = $reminders;
	}

	/**
	 * Creates a default reminders repository if none has been specified.
	 *
	 * @return \Cartalyst\Sentry\Reminders\IlluminateReminderRepository
	 */
	protected function createRemindersRepository()
	{
		$model = 'Cartalyst\Sentry\Reminders\EloquentReminder';

		$users = $this->getUserRepository();

		return new IlluminateReminderRepository($users, $model);
	}

	/**
	 * Returns all accessible methods on the associated user repository.
	 *
	 * @return array
	 */
	protected function getUserMethods()
	{
		if (empty($this->userMethods))
		{
			$users = $this->getUserRepository();

			$methods = get_class_methods($users);
			$banned = array('__construct');

			foreach ($banned as $method)
			{
				$index = array_search($method, $methods);

				if ($index !== false)
				{
					unset($methods[$index]);
				}
			}

			$this->userMethods = $methods;
		}

		return $this->userMethods;
	}

	/**
	 * Dynamically pass missing methods to Sentry.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		$methods = $this->getUserMethods();

		if (in_array($method, $methods))
		{
			$users = $this->getUserRepository();

			return call_user_func_array(array($users, $method), $parameters);
		}

		if (starts_with($method, 'findGroupBy'))
		{
			$groups = $this->getGroupRepository();

			$method = 'findBy'.substr($method, 11);

			return call_user_func_array(array($groups, $method), $parameters);
		}

		$methods = array('getGroups', 'inGroup', 'hasAccess', 'hasAnyAccess');
		$className = get_class($this);

		if (in_array($method, $methods))
		{
			$user = $this->getUser();

			if ($user === null)
			{
				throw new \BadMethodCallException("Method {$className}::{$method}() can only be called if a user is logged in.");
			}

			return call_user_func_array(array($user, $method), $parameters);
		}

		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}
