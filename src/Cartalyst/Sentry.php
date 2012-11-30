<?php namespace Cartalyst;
/**
 * Part of the Sentry bundle for Laravel.
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
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\ProviderInterface;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\GroupInterface;

use Illuminate\Session\Store as SessionStore;
use Illuminate\Session\CookieStore;
use Illuminate\CookieJar;


/**
 * Sentry Auth class
 */
class Sentry
{
	/**
	 * The current user
	 *
	 * @var  Cartalyst\Sentry\UserInterface
	 */
	protected $user;

	/**
	 * Provider Interface
	 *
	 * @var  Cartalyst\Sentry\ProviderInterface
	 */
	protected $provider;

	/**
	 * Session provider sentry should use
	 *
	 * @var  Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Throttle Enabled
	 *
	 */
	protected $throttle = true;

	/**
	 * Initantiate the Auth class and inject dependencies
	 *
	 * @param   userModel  User Object
	 * @return  object  Auth Instance
	 */
	public function __construct(ProviderInterface $providerInterface)
	{
		// set dependencies
		$this->provider = $providerInterface;
	}

	/**
	 * Authenticate a user
	 *
	 * @param   string  login value
	 * @param   string  password value
	 * @param   bool    remember user
	 * @return  bool
	 */
	public function authenticate(array $credentials, $remember = false)
	{
		// run logout to clear any current sentry session
		$this->logout();

		// get a user object and find the required authentication column
		$user = $this->user();
		$login = $user->getLoginColumn();

		// make sure the required login column is passed
		if ( ! array_key_exists($login, $credentials))
		{
			throw new \Exception('login field required');
		}

		// if throttle is enabled, check throttle status
		if ($this->throttle and ! $this->provider->throttleInterface()->check($credentials[$login]))
		{
			return false;
		}

		// find user by passed credentials
		$user = $user->findByCredentials($credentials);

		// log user in if found
		if ($user)
		{
			$this->login($user, $remember, false);

			return true;
		}

		// user not found
		// add attempt if throttle is enabled
		if ($this->throttle)
		{
			$this->provider->throttleInterface()->addAttempt($credentials[$login]);
		}

		return false;
	}

	/**
	 * Authenticate a user and remember them
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  bool
	 */
	public function authenticateAndRemember($login, $password)
	{
		return $this->authenticate($login, $password, true);
	}

	/**
	 * Log a user in
	 *
	 * @param   User  $user
	 */
	public function login(UserInterface $user, $remember = false, $checkThrottle = true)
	{
		// make sure the user exists
		if ( ! $this->user()->findByLogin($user->{$user->getLoginColumn()}))
		{
			throw new UserNotExistsException();
		}

		// check for throttle
		if ($this->throttle)
		{
			if ($checkThrottle and ! $this->provider->throttleInterface()->check($user->{$user->getLoginColumn()}))
			{
				return false;
			}

			$this->provider->throttleInterface()->clearAttempts($user->{$user->getLoginColumn()});
		}

		// check if the user is activated
		if ( ! $user->isActivated())
		{
			throw new UserNotActivatedException();
		}

		$user->clearResetPassword();

		$this->user = $user;

		// set sessions
	}

	/**
	 * Log a user out
	 *
	 * @return  void
	 */
	public function logout()
	{
		$this->user = null;

		// clear sessions
	}

	/**
	 * Check to see if the user is logged in
	 *
	 * @return  bool
	 */
	public function check()
	{
		return ! is_null($this->user);
	}

	/**
	 * Returns active authenticated user
	 *
	 * @return Sentry\UserInterface
	 */
	public function activeUser()
	{
		return $this->user;
	}

	/**
	 * Gets a user object
	 *
	 * @param   string  $login
	 * @return  Sentry\UserInterface|null
	 */
	public function user()
	{
		return $this->provider->userInterface();
	}

	/**
	 * Gets a group object
	 */
	public function group()
	{
		return $this->provider->groupInterface();
	}

	/**
	 * Enable throttling
	 *
	 * @param   integer  $limit
	 * @param   integer  $minutes
	 * @throws
	 */
	public function enableThrottle($limit = null, $minutes = null)
	{
		if ( ! is_int($limit) and ! is_null($limit))
		{
			throw new \Exception('throttle exception');
		}

		if ( ! is_int($minutes) and ! is_null($minutes))
		{
			throw new \Exception('throttle exception');
		}

		$this->throttle = true;
		! is_null($limit) and $this->provider->throttleInterface()->setAttemptLimit($limit);
		! is_null($minutes) and $this->provider->throttleInterface()->setSuspensionTime($minutes);
	}

	/**
	 * Disables throttling
	 */
	public function disableThrottle()
	{
		$this->throttle = false;
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->provider, $method), $args);
	}
}