<?php namespace Cartalyst\Sentry\Facades\CI;
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

use Cartalyst\Sentry\Cookies\CICookie;
use Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Sessions\CISession;
use Cartalyst\Sentry\Sentry as BaseSentry;
use Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
use Cartalyst\Sentry\Facades\Generic\ConnectionResolver;
use Database_Connection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use PDO;

class Sentry {

	/**
	 * Sentry instance.
	 *
	 * @var Cartalyst\Sentry\Sentry
	 */
	protected static $instance;

	protected static $pdoOptions = array(
		PDO::ATTR_CASE              => PDO::CASE_LOWER,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false,
	);

	public static function instance()
	{
		if (static::$instance === null)
		{
			static::$instance = static::createSentry();
		}

		return static::$instance;
	}

	/**
	 * Creates an instance of Sentry.
	 *
	 * @return Cartalyst\Sentry\Sentry
	 */
	public static function createSentry()
	{
		// Get some resources
		$ci =& get_instance();
		$ci->load->driver('session');

		$hasher           = new NativeHasher;
		$session          = new CISession($ci->session);
		$cookie           = new CICookie($ci->input);
		$groupProvider    = new GroupProvider;
		$userProvider     = new UserProvider($hasher);
		$throttleProvider = new ThrottleProvider($userProvider);

		static::createDatabaseResolver();

		return new BaseSentry(
			$hasher,
			$session,
			$cookie,
			$groupProvider,
			$userProvider,
			$throttleProvider
		);
	}

	public static function createDatabaseResolver()
	{
		// Get some resources
		$ci =& get_instance();
		$ci->load->database();
		$db = $ci->db;

		// Let's connect and get the PDO instance
		$pdo = $db->db_pconnect();

		// Validate PDO
		if ( ! $pdo instanceof PDO)
		{
			throw new \RuntimeException("Sentry will only work with PDO database connections.");
		}

		foreach (static::$pdoOptions as $key => $value)
		{
			$pdo->setAttribute($key, $value);
		}

		// If Eloquent doesn't exist, then we must assume they are using their own providers.
		if ( ! class_exists('Illuminate\Database\Eloquent\Model'))
		{
			return;
		}

		$driverName  = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
		$tablePrefix = substr($db->dbprefix('.'), 0, -1);

		Eloquent::setConnectionResolver(new ConnectionResolver($pdo, $driverName, $tablePrefix));
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