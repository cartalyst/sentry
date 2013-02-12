<?php namespace Cartalyst\Sentry\Facades\Native;
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

use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;
use Cartalyst\Sentry\Groups\ProviderInterface as GroupProviderInterface;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Sentry as BaseSentry;
use Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use Cartalyst\Sentry\Throttling\ProviderInterface as ThrottleProviderInterface;
use Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
use Cartalyst\Sentry\Users\ProviderInterface as UserProviderInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;
use PDO;

class Sentry {

	/**
	 * Sentry instance.
	 *
	 * @var Cartalyst\Sentry\Sentry
	 */
	protected static $instance;

	/**
	 * Whether the Database Connection hss been setup.
	 *
	 * @var bool
	 */
	protected static $dbSetup = false;

	public static function instance(
		HasherInterface $hasher = null,
		SessionInterface $session = null,
		CookieInterface $cookie = null,
		GroupProviderInterface $groupProvider = null,
		UserProviderInterface $userProvider = null,
		ThrottleProviderInterface $throttleProvider = null)
	{
		if (static::$instance === null)
		{
			static::$instance = static::createSentry(
				$hasher,
				$session,
				$cookie,
				$groupProvider,
				$userProvider,
				$throttleProvider
			);
		}

		return static::$instance;
	}

	/**
	 * Creates a Sentry instance.
	 *
	 * @param  Cartalys\Sentry\Hashing\HasherInterface      $hasher
	 * @param  Cartalys\Sentry\Sessions\SessionInterface    $session
	 * @param  Cartalys\Sentry\Cookies\CookieInterface      $cookie
	 * @param  Cartalys\Sentry\Groups\GroupProvider         $groupProvider
	 * @param  Cartalys\Sentry\Users\UserProvider           $userProvider
	 * @param  Cartalys\Sentry\Throttling\ThrottleProvider  $throttleProvider
	 * @return Cartalyst\Sentry\Sentry
	 */
	public static function createSentry(
		HasherInterface $hasher = null,
		SessionInterface $session = null,
		CookieInterface $cookie = null,
		GroupProviderInterface $groupProvider = null,
		UserProviderInterface $userProvider = null,
		ThrottleProviderInterface $throttleProvider = null)
	{

		if ( ! static::$dbSetup)
		{
			throw new \RuntimeException(sprintf('You must first setup the database connection by calling %s::setupDatabaseResolver().', __NAMESPACE__));
		}

		$hasher           = $hasher ?: new NativeHasher;
		$session          = $session ?: new NativeSession;
		$cookie           = $cookie ?: new NativeCookie;
		$groupProvider    = $groupProvider ?: new GroupProvider;
		$userProvider     = $userProvider ?: new UserProvider($hasher);
		$throttleProvider = $throttleProvider ?: new ThrottleProvider($userProvider);

		return new BaseSentry(
			$hasher,
			$session,
			$cookie,
			$groupProvider,
			$userProvider,
			$throttleProvider
		);
	}

	/**
	 * Sets up the Eloquent Connection Resolver with the given PDO connection.
	 *
	 * @param  PDO    $pdo
	 * @param  string $driverName
	 * @param  string $tablePrefix
	 * @return void
	 */
	public static function setupDatabaseResolver(PDO $pdo, $driverName = null, $tablePrefix = '')
	{
		// If Eloquent doesn't exist, then we must assume they are using their own providers.
		if (class_exists('Illuminate\Database\Eloquent\Model'))
		{
			if (is_null($driverName))
			{
				$driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
			}
			Eloquent::setConnectionResolver(new ConnectionResolver($pdo, $driverName, $tablePrefix));
		}
		static::$dbSetup = true;
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::instance();

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

}
