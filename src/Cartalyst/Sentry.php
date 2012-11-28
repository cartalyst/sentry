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
	public function authenticate($login, $password, $remember = false)
	{
		// run logout to clear any current sentry session
		$this->logout();

		if ($this->throttle and ! $this->provider->throttleInterface()->check($login))
		{
			echo 'disallow login';
			exit;
		}

		// validate user
		$user = $this->provider->userInterface()->findByCredentials($login, $password);

		if ($user)
		{
			$this->login($user, $remember, false);

			return true;
		}

		// add attempt if throttle is enabled
		if ($this->throttle)
		{
			$this->provider->throttleInterface()->addAttempt($login);
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
		if ( ! $user->{$user->getLoginColumn()})
		{
			echo 'invalid user';
			exit;
		}

		// check for throttle
		if ($this->throttle)
		{
			if ($checkThrottle and ! $this->provider->throttleInterface()->check($user->{$user->getLoginColumn()}))
			{
				echo 'disallow login';
				exit;
			}

			$this->provider->throttleInterface()->clearAttempts($user->{$user->getLoginColumn()});
		}

		$this->user = $this->provider->clearResetPassword($user);

		echo 'logged in!';
		exit;

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
	 * Get the current user or requested user by login
	 *
	 * @param   string  $login
	 * @return  Sentry\UserInterface|null
	 */
	public function user()
	{
		return $this->provider->userInterface();
	}

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

	public function disableThrottle()
	{
		$this->throttle = false;
	}

	/**
	 *
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->provider, $method), $args);
	}
}