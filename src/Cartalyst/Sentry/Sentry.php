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
use Cartalyst\Sentry\Groups\ProviderInterface as GroupProviderInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Throttling\ProviderInterface as ThrottleProviderInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\ProviderInterface as UserProviderInterface;
use Cartalyst\Sentry\Users\UserInterface;
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
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $groupProvider
	 * @param  Cartalyst\Sentry\Users\UserInterface  $userProvider
	 * @param  Cartalyst\Sentry\Throttling\ThrottleInterface  $throttleProvider
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

	/**
	 * Registers a user by giving the required credentials
	 * and an optional flag for whether to activate the user.
	 *
	 * @param  array  $credentials
	 * @param  bool   $activate
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function register(array $credentials, $activate = false)
	{
		$user = $this->userProvider->create($credentials);

		if ($activate)
		{
			$user->attemptActivation($user->getActivationCode());
		}

		return $this->user = $user;
	}


	/**
	 * Attempts to authenticate the given user
	 * according to the passed credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $remember
	 * @return Cartalyst\Sentry\Users\UserInterface
	 * @throws Cartalyst\Sentry\Users\LoginRequiredException
	 * @throws Cartalyst\Sentry\Users\PasswordRequiredException
	 * @throws Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function authenticate(array $credentials, $remember = false)
	{
		// We'll default to the login name field, but fallback to a hard-coded
		// 'login' key in the array that was passed.
		$loginName = $this->userProvider->getEmptyUser()->getUserLoginName();
		$loginCredentialKey = (isset($credentials[$loginName])) ? $loginName : 'login';

		if (empty($credentials[$loginCredentialKey]))
		{
			throw new LoginRequiredException("The $loginCredentialKey attribute is required.");
		}

		if (empty($credentials['password']))
		{
			throw new PasswordRequiredException('The password attribute is required.');
		}

		$throttlingEnabled = $this->throttleProvider->isEnabled();

		try
		{
			$user = $this->userProvider->findByCredentials($credentials);
		}
		catch (UserNotFoundException $e)
		{
			if ($throttlingEnabled)
			{
				if (isset($credentials[$loginCredentialKey]))
				{
					$throttle = $this->throttleProvider->findByUserLogin($credentials[$loginCredentialKey]);
					$throttle->addLoginAttempt();
				}
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
		return $this->user;
	}

	/**
	 * Alias for authenticating with the remember flag checked.
	 *
	 * @param  array  $credentials
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function authenticateAndRemember(array $credentials)
	{
		return $this->authenticate($credentials, true);
	}

	/**
	 * Check to see if the user is logged in and activated.
	 *
	 * Upon success, the logged in user is returned. Upon
	 * failure, "false" is returned.
	 *
	 * @return mixed
	 */
	public function check()
	{
		if ( ! $this->user)
		{
			// Check session first, follow by cookie
			if ( ! $userArray = $this->session->get() and ! $userArray = $this->cookie->get())
			{
				return false;
			}

			// Now check our user is an array with two elements,
			// the username followed by the persist code
			if ( ! is_array($userArray) or count($userArray) !== 2)
			{
				return false;
			}

			list($login, $persistCode) = $userArray;

			// Let's find our user
			try
			{
				$user = $this->getUserProvider()->findByLogin($login);
			}
			catch (UserNotFoundException $e)
			{
				return false;
			}

			// Great! Let's check the session's persist code
			// against the user. If it fails, somebody has tampered
			// with the cookie / session data and we're not allowing
			// a login
			if ( ! $user->checkPersistCode($persistCode))
			{
				return false;
			}

			// Now we'll set the user property on Sentry
			$this->user = $user;
		}

		// Let's check our cached user is indeed activated
		if ( ! $user = $this->getUser() or ! $user->isActivated())
		{
			return false;
		}

		return $user;
	}

	/**
	 * Logs in the given user and sets properties
	 * in the session.
	 *
	 * @param  Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 * @throws Cartalyst\Sentry\Users\UserNotActivatedException
	 */
	public function login(UserInterface $user, $remember = false)
	{
		if ( ! $user->isActivated())
		{
			$login = $user->getUserLogin();
			throw new UserNotActivatedException("Cannot login user [$login] as they are not activated.");
		}

		$this->user = $user;

		// Create an array of data to persist to the session and / or cookie
		$toPersist = array($user->getUserLogin(), $user->createPersistCode());

		// Set sessions
		$this->session->put($toPersist);

		if ($remember)
		{
			$this->cookie->forever($toPersist);
		}
	}

	/**
	 * Logs the current user out.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->user = null;

		$this->session->forget();
		$this->cookie->forget();
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

	public function setGroupProvider(GroupProviderInterface $groupProvider)
	{
		$this->groupProvider = $groupProvider;
	}

	public function getGroupProvider()
	{
		return $this->groupProvider;
	}

	public function setUserProvider(UserProviderInterface $userProvider)
	{
		$this->userProvider = $userProvider;
	}

	public function getUserProvider()
	{
		return $this->userProvider;
	}

	public function setThrottleProvider(ThrottleProviderInterface $throttleProvider)
	{
		$this->throttleProvider = $throttleProvider;
	}

	public function getThrottleProvider()
	{
		return $this->throttleProvider;
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (isset($this->user))
		{
			return call_user_func_array(array($this->user, $method), $parameters);
		}

		throw new \BadMethodCallException("Method [$method] is not supported by Sentry or no User has been set on Sentry to access shortcut method.");
	}

}
