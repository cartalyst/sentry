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

use Closure;

class Sentry {

	/**
	 * The current cached, logged in user.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * User repository.
	 *
	 * @var \Cartalyst\Sentry\Users\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * Flag for whether checkpoints are enabled.
	 *
	 * @var bool
	 */
	protected $checkpoints = true;

	/**
	 * The persistence driver (the class which actually manages sessions).
	 *
	 * @var \Cartalyst\Sentry\Persistence\PersistenceInterface
	 */
	protected $persistance;

	/**
	 * Event dispatcher.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Registers a user. You may provide a callback to occur before the user
	 * is saved, or provide a true boolean as a shortcut to activation.
	 *
	 * @param  array  $credentials
	 * @param  \Closure|bool  $callbcak
	 * @return \Cartalyst\Sentry\Users\UserInteface|bool
	 */
	public function register(array $credentials, $callback = null)
	{
		$valid = $this->users->validForCreation($credentials);

		if ($callback === true)
		{
			$me = $this;
			$callback = function(UserInterface $user)
			{
				$activation = $me->activate($user);

				if ($activation === false)
				{
					return false;
				}

				return $user;
			};
		}
		elseif ( ! $callback instanceof Closure)
		{
			throw new \InvalidArgumentException('You must provide a closure or true boolean.');
		}

		return $this->users->create($credentials, $callback);
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
	public function activate(UserInterface $user)
	{
		return $this->activations->activate($user);
	}

	/**
	 * Checks to see if a user is logged in.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function check()
	{
		$code = $this->persistance->check();

		if ($code === null)
		{
			return false;
		}

		$user = $this->users->findByPersistenceCode($code);

		if ($user === null)
		{
			return false;
		}

		if ( ! $this->cycleCheckpoints($user))
		{
			return false;
		}

		return $user;
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
	 * Authenticates a user, with "remember" flag.
	 *
	 * @param  array  $credentials
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function authenticate(array $credentials, $remember = false)
	{
		$user = $this->users->findByCredentials($credentials);

		$method = ($remember === true) ? 'loginAndRemember' : 'login';

		return $this->$method($user);
	}

	/**
	 * Authenticates a user, with "remember" flag.
	 *
	 * @param  array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function authenticateAndRemember(array $credentials)
	{
		return $this->authenticate($credentials, true);
	}

	/**
	 * Forces an authentication to bypass checkpoints.
	 *
	 * @param  array  $credentials
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceAuthenticate(array $credentials, $remmeber = false)
	{
		return $this->bypassCheckpoints(function($sentry) use ($credentials, $remember)
		{
			return $sentry->authenticate($credentials, $remember);
		});
	}

	/**
	 * Forces an authentication to bypass checkpoints, with "remember" flag.
	 *
	 * @param  array  $credentials
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceAuthenticateAndRemember(array $credentials)
	{
		return $this->forceAuthenticate($credentials, true);
	}

	/**
	 * Persists a login for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function login(UserInterface $user, $remmeber = false)
	{
		if ( ! $this->cycleCheckpoints($user))
		{
			return false;
		}

		$method = ($remember === true) ? 'addAndRemember' : 'add';

		return $this->persistance->$method($user);
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

		return $this->persistance->$method($this->user);
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
	 * Add a new checkpoint to Sentry.
	 *
	 * @param  \Closure|string  $checkpoint
	 * @param  int  $priority
	 * @return void
	 */
	public function addCheckpoint($checkpoint, $priority = 0)
	{
		$this->registerEvent('checkpoint', $callback, $priority);
	}

	/**
	 * Cycles through all registered checkpoints for a user. Checkpoints may
	 * throw their own exceptions, however, if just one returns false, the
	 * cycle fails.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	protected function cycleCheckpoints(UserInterface $user)
	{
		if ($this->checkpoints === false)
		{
			return true;
		}

		$response = $this->fireEvent('checkpoint', $user);

		return ($response !== false);
	}

	protected function registerEvent($event, $callback, $priority = 0)
	{
		$this->dispatcher->listen("sentry.{$event}", $callback, $priority);
	}

	protected function fireEvent($event, $payload = array())
	{
		return $this->dispatcher->fire("sentry.{$event}", $payload);
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
			return call_user_func_array(array($this->users, $method), $parameters);
		}

		$className = get_class($this);

		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}
