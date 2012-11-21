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
	 * @var  Cartalyst\Sentry\Model\User
	 */
	protected $user;

	/**
	 * The user provider to authenticate with
	 *
	 * @var  Cartalyst\Sentry\Provider\User
	 */
	protected $provider;

	/**
	 * Session provider sentry should use
	 *
	 * @var  Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Initantiate the Auth class and inject dependencies
	 *
	 * @param   userModel  User Object
	 * @return  object  Auth Instance
	 */
	public function __construct(UserProvider $userProvider = null)
	{
		// set dependencies
		$this->provider = ($userProvider) ?: new Sentry\User();
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

		// validate user
		$user = $this->provider->findByCredentials($login, $password);

		if ($user)
		{
			$this->login($user, $remember);

			return true;
		}

		return false;
	}

	/**
	 * Authenticate a user and remember them
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  bool
	 * @throws  SentryException
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
	public function login($user, $remember = false)
	{
		$user = $this->provider->clearResetPassword($user);

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
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($login, $activationCode)
	{
		return $this->provider->activate($login, $activationCode);
	}

	/**
	 * Reset a user's password
	 *
	 * @param   string   $login
	 * @param   string   $password
	 * @return  string|false
	 */
	public function resetPassword($login, $password)
	{
		return $this->provider->resetPassword($login, $password);

	}

	/**
	 * Confirm a password reset request
	 *
	 * @param   string  $login
	 * @param   string  $resetCode
	 * @return  bool
	 */
	public function confirmResetPassword($login, $resetCode)
	{
		return $this->provider->confirmResetPassword($login, $resetCode);
	}

}