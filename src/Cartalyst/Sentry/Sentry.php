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

use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Group\ProviderInterface as GroupProviderInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Throttling\ProviderInterface as ThrottleProviderInterface;
use Cartalyst\Sentry\Users\ProviderInterface as UserProviderInterface;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\UserNotActivatedException;

class Sentry {

	/**
	 * The user that's been retrieved and is used
	 * for authentication. Authentication methods
	 * are available for finding the user to set
	 * here.
	 *
	 * @var Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * The hasher used in Sentry. It's used for
	 * protected attributes of users.
	 *
	 * @var Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * The session driver used by Sentry.
	 *
	 * @var Cartalyst\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * The cookie driver used by Sentry.
	 *
	 * @var Cartalyst\Sentry\Cookies\CookieInterface
	 */
	protected $cookie;

	/**
	 * The group provider, used for retrieving
	 * objects which implement the Sentry group
	 * interface.
	 *
	 * @var Cartalyst\Sentry\Groups\ProviderInterface
	 */
	protected $groupProvider;

	/**
	 * The user provider, used for retrieving
	 * objects which implement the Sentry user
	 * interface.
	 *
	 * @var Cartalyst\Sentry\Users\ProviderInterface
	 */
	protected $userProvider;

	/**
	 * The throttle provider, used for retrieving
	 * objects which implement the Sentry throttling
	 * interface.
	 *
	 * @var Cartalyst\Sentry\Throttling\ProviderInterface
	 */
	protected $throttleProvider;

	/**
	 * Create a new Sentry object.
	 *
	 * @param  Cartalyst\Sentry\Hashing\HasherInterface  $hasher
	 * @param  Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @param  Cartalyst\Sentry\Cookies\CookieInterface  $cookie
	 * @param  Cartalyst\Sentry\Groups\ProviderInterface  $groupProvider
	 * @param  Cartalyst\Sentry\Users\ProviderInterface  $userProvider
	 * @param  Cartalyst\Sentry\Throttling\ProviderInterface  $throttleProvider
	 */
	public function __construct(
		HasherInterface $hasher,
		SessionInterface $session,
		CookieInterface $cookie,
		GroupProviderInterface $groupProvider,
		UserProviderInterface $userProvider,
		ThrottleProviderInterface $throttleProvider
	)
	{
		$this->hasher           = $hasher;
		$this->session          = $session;
		$this->cookie           = $cookie;
		$this->groupProvider    = $groupProvider;
		$this->userProvider     = $userProvider;
		$this->throttleProvider = $throttleProvider;
	}

	public function authenticate(array $credentials, $remember = false)
	{
		$throttlingEnabled = $this->throttleProvider->isEnabled();

		try
		{
			$user = $this->userProvider->findByCredentials($credentials);
		}
		catch (UserNotFoundException $e)
		{
			// @todo, we should check what the login column is, e.g. 'email'
			// and match against those credentials
			if ($throttlingEnabled and isset($credentials['login']))
			{
				$throttle = $this->throttleProvider->findByUserLogin($credentials['login']);
				$throttle->addLoginAttempt();
			}

			throw $e;
		}

		if ($throttlingEnabled)
		{
			$throttle = $this->throttleProvider->findByUserId($user->getUserId());
			$throttle->check();
			$throttle->clearLoginAttempts();
		}

		$user->clearResetPassword();

		$this->login($user, $remember);
	}

	public function authenticateAndRembmer(array $credentials)
	{
		return $this->authenticate($credentials, true);
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

	public function login(UserInterface $user, $remember = false)
	{
		$this->user = $user;

		// Set sessions
		$this->session->put($this->session->getKey(), $user);

		if ($remember)
		{
			$this->cookie->forever($this->cookie->getKey(), $user);
		}
	}

	public function logout()
	{
		unset($this->user);

		$this->session->flush();
		$this->cookie->flush();
	}

	/**
	 * Sets the user to be used by Sentry.
	 *
	 * @param  Cartalyst\Sentry\Users\UserInterface
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Returns the current user being
	 * used by Sentry, if any.
	 *
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		if (isset($this->user))
		{
			return call_user_func_array(array($this->user, $method), $parameters);
		}

		throw new \BadMethodCallException("Method [$method] is not supported by Sentry.");
	}

}