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
	 * Throttle repository.
	 *
	 * @var \Cartalyst\Sentry\Throttling\ThrottleRepositoryInterface
	 */
	protected $throttle;

	/**
	 * Flag for whether throttling is enabled in Sentry.
	 *
	 * @var bool
	 */
	protected $throttling = true;

	/**
	 * The persistence driver (the class which actually manages sessions).
	 *
	 * @var \Cartalyst\Sentry\Persistence\PersistenceInterface
	 */
	protected $persistance;

	/**
	 * The cached IP address, used for throttling checks.
	 *
	 * @var string
	 */
	protected $ipAddress;

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

		$this->checkThrottle();

		return $user;
	}

	/**
	 * Checks to see if a user is logged in, bypassing throttling
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceCheck()
	{
		return $this->bypassThrottling(function($sentry)
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

		$this->checkThrottle($user);

		return $user;
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
	 * Forces an authentication to bypass throttling.
	 *
	 * @param  array  $credentials
	 * @param  bool  $remember
	 * @return \Cartalyst\Sentry\Users\UserInterface|bool
	 */
	public function forceAuthenticate(array $credentials, $remmeber = false)
	{
		return $this->bypassThrottling(function($sentry) use ($credentials, $remember)
		{
			return $sentry->authenticate($credentials, $remember);
		});
	}

	/**
	 * Forces an authentication to bypass throttling, with "remember" flag.
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
		$method = ($remember === true) ? 'addAndRemember' : 'add';

		return $this->persistance->$method($user);
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
	 * Pass a closure to Sentry to bypass throttling.
	 *
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function bypassThrottling(Closure $callback)
	{
		// Cache the throttling status
		$throttling = $this->throttling;
		$this->throttling = false;

		// Fire the callback
		$result = $callback($this);

		// Reset throttling
		$this->throttling = $throttling;

		return $result;
	}

	/**
	 * Performs a throttle check.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @throws \RuntimeException
	 */
	protected function checkThrottle(UserInterface $user = null)
	{
		if ($this->throttling === false)
		{
			return true;
		}

		$globalDelay = $this->throttle->globalDelay();

		if ($globalDelay > 0)
		{
			throw new \RuntimeException("Gobal throttling prohibits users from logging in for another [$globalDelay] second(s).");
		}

		if (isset($this->ipAddress))
		{
			$ipDelay = $this->throttle->ipDelay();

			if ($ipDelay > 0)
			{
				throw new \RuntimeException("IP address throttling prohibits you from logging in for another [$ipDelay] second(s).");
			}
		}

		if (isset($user))
		{
			$userDelay = $this->throttle->userDelay($user);

			if ($ipDelay > 0)
			{
				throw new \RuntimeException("User throttling prohibits your account being accessed in for another [$ipDelay] second(s).");
			}
		}

		return true;
	}

}
