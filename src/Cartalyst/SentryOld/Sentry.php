<?php namespace Cartalyst\Sentry;
/**
 * Part of the Sentry Package.
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
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class Sentry {

	/**
	 * The current user.
	 *
	 * @var Cartalyst\Sentry\UserInterface
	 */
	protected $user;

	/**
	 * Provider Interface.
	 *
	 * @var Cartalyst\Sentry\ProviderInterface
	 */
	protected $provider;

	/**
	 * Session provider sentry should use.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Session provider sentry should use.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $cookie;

	/**
	 * Initantiate the Auth class and inject dependencies.
	 *
	 * @param  Cartalyst\Sentry\ProviderInterface  $provider
	 * @param  Cartalyst\Sentry\SessionInterface  $session
	 * @param  Cartalyst\Sentry\CookieInterface  $cookie
	 * @return void
	 */
	public function __construct(ProviderInterface $provider, SessionInterface $session, CookieInterface $cookie)
	{
		// Set dependencies
		$this->provider = $provider;
		$this->session  = $session;
		$this->cookie   = $cookie;
	}

	/**
	 * Authenticate a user
	 *
	 * @param  string  login value
	 * @param  string  password value
	 * @param  bool    remember user
	 * @return bool
	 * @throws LoginFieldRequiredException,
	 */
	public function authenticate(array $credentials, $remember = false)
	{
		// Run logout to clear any current sentry session
		$this->logout();

		try
		{
			// Find user by passed credentials
			$user = $this->user()->findByCredentials($credentials);
		}
		catch (UserNotFoundException $e)
		{
			// Add attempt if throttle is enabled
			if ($this->provider->throttleInterface()->isEnabled())
			{
				// Get a user object and find the required authentication column
				$login = $this->user()->getLoginColumn();

				if ( ! $this->provider->throttleInterface()->check($credentials[$login]))
				{
					return false;
				}

				$this->provider->throttleInterface()->addAttempt($credentials[$login]);

				unset($login);
			}

			return false;
		}

		if ($this->provider->throttleInterface()->isEnabled())
		{
			// Before we proceed, check the users' throttle status
			if ( ! $this->provider->throttleInterface()->check($credentials[$user->getLoginColumn()]))
			{
				return false;
			}

			// No exception was thrown for checking, go ahead and clear everything
			$this->provider->throttleInterface()->clearAttempts($credentials[$user->getLoginColumn()]);
		}

		$user->clearResetPassword();

		$this->login($user, $remember, false);

		return true;
	}

	/**
	 * Authenticate a user and remember them
	 *
	 * @param  string  $login
	 * @param  string  $password
	 * @return bool
	 */
	public function authenticateAndRemember(array $credentials)
	{
		return $this->authenticate($credentials, true);
	}

	/**
	 * Log a user in
	 *
	 * @param  UserInterface  $user
	 * @return void
	 */
	public function login(UserInterface $user, $remember = false)
	{
		// Make sure the user exists
		if ( ! $user->exists)
		{
			throw new UserNotFoundException;
		}

		// Check if the user is activated
		if ( ! $user->isActivated())
		{
			throw new UserNotActivatedException;
		}

		$this->user = $user;

		// Set sessions
		$this->session->put($this->session->getKey(), $user);

		if ($remember)
		{
			$this->cookie->forever($this->cookie->getKey(), $user);
		}
	}

	/**
	 * Log a user in
	 *
	 * @param  UserInterface  $user
	 * @return void
	 */
	public function loginAndRemember(UserInterface $user)
	{
		return $this->login($user, true);
	}

	/**
	 * Log a user out
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->user = null;

		$this->session->flush();
		$this->cookie->flush();
	}

	/**
	 * Check to see if the user is logged in
	 *
	 * @return bool
	 */
	public function check()
	{
		if ($this->user)
		{
			return true;
		}

		// Check session
		$this->user = $this->session->get($this->session->getKey());

		// Check for cookie
		if ( ! $this->user)
		{
			$this->user = $this->cookie->get($this->cookie->getKey());
		}

		return ! is_null($this->user);
	}

	/**
	 * Returns active authenticated user
	 *
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function activeUser()
	{
		if ( ! $this->check())
		{
			return null;
		}

		$this->user = $this->provider->userInterface()->findById($this->user->id);

		return $this->user;
	}

	/**
	 * Gets a user object
	 *
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function user()
	{
		return $this->provider->userInterface();
	}

	/**
	 * Gets a group object
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function group()
	{
		return $this->provider->groupInterface();
	}

	/**
	 * Gets a throttle object
	 *
	 * @return Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttle()
	{
		return $this->provider->throttleInterface();
	}

	/**
	 * Dynamically pass methods to the the Sentry Provider.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->provider, $method), $args);
	}

}