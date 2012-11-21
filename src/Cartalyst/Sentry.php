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
	 * The user
	 *
	 * @var  Cartalyst\Sentry\Model\User
	 */
	protected $user;

	protected $userInterface;

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
	public function __construct(Sentry\UserInterface $userInterface)
	{
		// set dependencies
		$this->userInterface = $userInterface;
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
		$user = $this->userInterface->findByCredentials($login, $password);

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
	public function login(Sentry\UserInterface $user, $remember = false)
	{
		$user = $this->userInterface->clearResetPassword($user);

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

	public function getUserInterface()
	{
		return $this->userInterface;
	}

	public function setUserInterface(Sentry\UserInterface $userInterface)
	{
		$this->userInterface = $userInterface;
	}

	/**
	 * Dynamically pass methods to the user object, as that's
	 * the most likely object the developer will want to alter
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->userInterface, $method), $parameters);
	}

}