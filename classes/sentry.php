<?php

/**
 * Sentry Auth class
 *
 * @author  Daniel Petrie
 */

namespace Sentry;

class SentryAuthConfigException extends \Fuel_Exception {}

class SentryAuthException extends \Fuel_Exception {}

class SentryUserSuspendedException extends \Fuel_Exception {}

class Sentry
{
	protected static $login_id = null;

	protected static $table_users = null;

	protected static $table_suspend = null;

	protected static $limit = array();

	/**
	 * Prevent instantiation
	 */
	final private function __construct() {}

	/**
	 * Run when class is loaded
	 */
	public static function _init()
	{
		// load config
		\Config::load('sentry', true);

		// set static vars for later use
		static::$login_id = \Config::get('sentry.login_id');
		static::$table_users = \Config::get('sentry.table.users');
		static::$table_suspend = \Config::get('sentry.table.users_suspended');
		static::$limit = array(
			'enabled' => \Config::get('sentry.limit.enabled'),
			'attempts' => \Config::get('sentry.limit.attempts'),
			'time' => \Config::get('sentry.limit.time')
		);

		// validate config settings

		// login_id check
		if (static::$login_id != 'username' and static::$login_id != 'email')
		{
			throw new \SentryAuthConfigException(
				'Sentry Config Item: "login_id" must be set to "username" or "email".');
		}

		// limit checks
		if (static::$limit['enabled'] === true)
		{
			if ( ! is_int(static::$limit['attempts']) or static::$limit['attempts'] <= 0)
			{
				throw new \SentryAuthConfigException(
					'Sentry Config Item: "limit.attempts" must be an integer greater than 0');
			}

			if ( ! is_int(static::$limit['time']) or static::$limit['time'] <= 0)
			{
				throw new \SentryAuthConfigException(
					'Sentry Config Item: "limit.time" must be an integer greater than 0');
			}
		}

	}

	/**
	 * Returns Sentry_User Object
	 *
	 * @param int, string
	 */
	public static function user($id = null)
	{
		// if $id is passed - select that user
		if ($id)
		{
			return new Sentry_User($id);
		}
		// if session exists - default to user session
		else if(static::check())
		{
			$user = \Session::get('sentry_user');
			return new Sentry_User($user['id']);
		}
		// else return empty user
		return new Sentry_User();
	}

	/** User Authorization **/

	/**
	 * Log a User In
	 *
	 * @param string
	 * @param string
	 */
	public static function login($login_id, $password)
	{
		// get login attempts
		$attempts = static::check_attempts($login_id);

		// if attempts > limit - suspend the login/ip combo
		if ($attempts >= static::$limit['attempts'])
		{
			static::suspend($login_id);
		}

		// trim and reset vars
		$login_id = trim($login_id);
		$password = trim($password);

		// make sure vars have values
		if (empty($login_id) or empty($password))
		{
			static::add_attempt($login_id, $attempts);
			return false;
		}

		// check if user exists
		try
		{
			// get user from database
			$user = new Sentry_User($login_id);
		}
		catch (SentryUserNotFoundException $e)
		{
			static::add_attempt($login_id, $attempts);
			return false;
		}

		// make sure password matches
		if ( ! $user->check_password($password))
		{
			static::add_attempt($login_id, $attempts);
			return false;
		}

		// clear attempts for login since they got in
		static::clear_attempts($login_id);

		// set session vars
		\Session::set('sentry_user', array(
			'id' => (int) $user->get('id')
		));

		return true;
	}

	/**
	 * Is Logged In Check
	 */
	public static function check()
	{
		// get session
		$user = \Session::get('sentry_user');

		// invalid session values - kill the user session
		if ( ! isset($user['id']) or ! is_int($user['id']))
		{
			static::logout();
			return false;
		}

		return true;
	}

	/**
	 * Log current user out
	 */
	public static function logout()
	{
		\Session::delete('sentry_user');
	}

	/**
	 * Check Number of Login Attempts
	 *
	 * @param string
	 */
	protected static function check_attempts($login_id)
	{
		$result = \DB::select('attempts', 'last_attempt_at', 'unsuspend_at')
			->from(static::$table_suspend)
			->where('login_id', $login_id)
			->where('ip', \Input::real_ip())
			->execute()
			->current();

		// check if last attempt was more than 15 min ago - if so reset counter
		if ($result['last_attempt_at']
			and ($result['last_attempt_at'] + static::$limit['time']*60) <= time())
		{
			static::clear_attempts($login_id);
			return 0;
		}

		// check unsuspended time and clear if time is > than it
		if ($result['unsuspend_at'] and $result['unsuspend_at'] <= time())
		{
			static::clear_attempts($login_id);
			return 0;
		}

		return $result['attempts'];
	}

	/**
	 * Add Login Attempt
	 *
	 * @param string
	 * @param int
	 */
	protected static function add_attempt($login_id, $attempts = null)
	{
		if ($attempts)
		{
			$result = \DB::update(static::$table_suspend)
				->set(array(
					'attempts' => $attempts + 1,
					'last_attempt_at' => time(),
				))
				->where('login_id', $login_id)
				->where('ip', \Input::real_ip())
				->execute();
		}
		else
		{
			$result = \DB::insert(static::$table_suspend)
				->set(array(
					'login_id' => $login_id,
					'ip' => \Input::real_ip(),
					'attempts' => 1,
					'last_attempt_at' => time(),
				))
				->execute();
		}
	}

	/**
	 * Clear Login Attempts
	 *
	 * @param string
	 * @param string
	 */
	protected static function clear_attempts($login_id, $ip = null)
	{
		if ($ip === null)
		{
			$ip = \Input::real_ip();
		}

		$result = \DB::delete(static::$table_suspend)
			->where('login_id', $login_id)
			->where('ip', $ip)
			->execute();
	}

	/**
	 * Public Alias of Clear_Attempts
	 *
	 * @param string
	 * @param string
	 */
	public static function unsuspend($login_id, $ip = null)
	{
		static::clear_attempts($login_id, $ip);
	}

	/**
	 * Suspend
	 *
	 * @param string
	 * @param int
	 */
	protected static function suspend($login_id, $timeleft = null)
	{
		// only updates table if unsuspended at has no value
		$result = \DB::update(static::$table_suspend)
			->set(array(
				'suspended_at' => time(),
				'unsuspend_at' => time()+(static::$limit['time']*60),
			))
			->where('login_id', $login_id)
			->where('ip', \Input::real_ip())
			->where('unsuspend_at', null)
			->execute();

		throw new \SentryAuthSuspendedException(sprintf(
			'You have been suspended from trying to login into account "%s" for %s minutes.',
			$login_id, static::$limit['time']));
	}

}
