<?php
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

namespace Sentry;

use Config;
use Cookie;
use DB;
use Request;
use Session;
use Lang;
use Str;

class SentryException extends \Exception {}
class SentryConfigException extends SentryException {}

/**
 * Sentry Auth class
 */
class Sentry
{

	/**
	 * @var  string  Database instance
	 */
	 protected static $db_instance = null;

	/**
	 * @var  string  Holds the column to use for login
	 */
	protected static $login_column = null;

	/**
	 * @var  bool  Whether suspension feature should be used or not
	 */
	protected static $suspend = null;

	/**
	 * @var  Sentry_Attempts  Holds the Sentry_Attempts object
	 */
	protected static $attempts = null;

	/**
	 * @var  object  Caches the current logged in user object
	 */
	protected static $current_user = null;

	/**
	 * @var  array  Caches all users accessed
	 */
	protected static $user_cache = array();

	/**
	 * Prevent instantiation
	 */
	final private function __construct() {}

	/**
	 * Run when class is loaded
	 *
	 * @return  void
	 */
	public static function _init()
	{
		// set static vars for later use
		static::$login_column = trim(Config::get('sentry::sentry.login_column'));
		static::$suspend = trim(Config::get('sentry::sentry.limit.enabled'));
		$db_instance = trim(Config::get('sentry::sentry.db_instance'));

		// db_instance check
		if ( ! empty($db_instance) )
		{
			static::$db_instance = $db_instance;
		}

		// login_column check
		if (empty(static::$login_column))
		{
			throw new SentryConfigException(__('sentry::sentry.login_column_empty'));
		}

	}

	/**
	 * Get's either the currently logged in user or the specified user by id or Login
	 * Column value.
	 *
	 * @param   int|string  User id or Login Column value to find.
	 * @throws  SentryException
	 * @return  Sentry_User
	 */
	public static function user($id = null, $recache = false)
	{
		if ($id === null and $recache === false and static::$current_user !== null)
		{
			return static::$current_user;
		}
		elseif ($id !== null and $recache === false and isset(static::$user_cache[$id]))
		{
			return static::$user_cache[$id];
		}

		try
		{
			if ($id)
			{
				static::$user_cache[$id] = new Sentry_User($id);
				return static::$user_cache[$id];
			}
			// if session exists - default to user session
			elseif (static::check())
			{
				$user_id = Session::get(Config::get('sentry::sentry.session.user'));
				static::$current_user = new Sentry_User($user_id);
				return static::$current_user;
			}

			// else return empty user
			return new Sentry_User();
		}
		catch (SentryUserException $e)
		{
			throw new SentryException($e->getMessage());
		}
	}

	/**
	 * Get's either the currently logged in user's group object or the
	 * specified group by id or name.
	 *
	 * @param   int|string  Group id or or name
	 * @return  Sentry_User
	 */
	public static function group($id = null)
	{
		if ($id)
		{
			return new Sentry_Group($id);
		}

		return new Sentry_Group();
	}

	/**
	 * Gets the Sentry_Attempts object
	 *
	 * @return  Sentry_Attempts
	 */
	 public static function attempts($login_id = null, $ip_address = null)
	 {
	 	return new Sentry_Attempts($login_id, $ip_address);
	 }

	/**
	 * Attempt to log a user in.
	 *
	 * @param   string  Login column value
	 * @param   string  Password entered
	 * @param   bool    Whether to remember the user or not
	 * @return  bool
	 * @throws  SentryException
	 */
	public static function login($login_column_value, $password, $remember = false)
	{
		// log the user out if they hit the login page
		static::logout();

		// get login attempts
		if (static::$suspend)
		{
			$attempts = static::attempts($login_column_value, Request::ip());

			// if attempts > limit - suspend the login/ip combo
			if ($attempts->get() >= $attempts->get_limit())
			{
				try
				{
					$attempts->suspend();
				}
				catch(SentryUserSuspendedException $e)
				{
					throw new SentryException($e->getMessage());
				}
			}
		}

		// make sure vars have values
		if (empty($login_column_value) or empty($password))
		{
			return false;
		}

		// if user is validated
		if ($user = static::validate_user($login_column_value, $password, 'password'))
		{
			if (static::$suspend)
			{
				// clear attempts for login since they got in
				$attempts->clear();
			}

			// set update array
			$update = array();

			// if they wish to be remembers, set the cookie and get the hash
			if ($remember)
			{
				$update['remember_me'] = static::remember($login_column_value);
			}

			// if there is a password reset hash and user logs in - remove the password reset
			if ($user->get('password_reset_hash'))
			{
				$update['password_reset_hash'] = '';
				$update['temp_password'] = '';
			}

			$update['last_login'] = time();
			$update['ip_address'] = Request::ip();

			// update user
			if (count($update))
			{
				$user->update($update, false);
			}

			// set session vars
			Session::put(Config::get('sentry::sentry.session.user'), (int) $user->get('id'));
			Session::put(Config::get('sentry::sentry.session.provider'), 'Sentry');

			return true;
		}

		return false;
	}

	/**
	 * Force Login
	 *
	 * @param   int|string  user id or login value
	 * @param   provider    what system was used to force the login
	 * @return  bool
	 * @throws  SentryException
	 */
	public static function force_login($id, $provider = 'Sentry-Forced')
	{
		// check to make sure user exists
		if ( ! static::user_exists($id))
		{
			throw new SentryException(__('sentry::sentry.user_not_found'));
		}

		Session::set(Config::get('sentry::sentry.session.user'), $id);
		Session::set(Config::get('sentry::sentry.session.provider'), $provider);
		return true;
	}

	/**
	 * Checks if the current user is logged in.
	 *
	 * @return  bool
	 */
	public static function check()
	{
		// get session
		$user_id = Session::get(Config::get('sentry::sentry.session.user'));

		// invalid session values - kill the user session
		if ($user_id === null or ! is_numeric($user_id))
		{
			// if they are not logged in - check for cookie and log them in
			if (static::is_remembered())
			{
				return true;
			}

			//else log out
			static::logout();
			return false;
		}

		return true;
	}

	/**
	 * Logs the current user out.  Also invalidates the Remember Me setting.
	 *
	 * @return  void
	 */
	public static function logout()
	{
		Cookie::forget(Config::get('sentry::sentry.remember_me.cookie_name'));
		Session::forget(Config::get('sentry::sentry.session.user'));
		Session::forget(Config::get('sentry::sentry.session.provider'));
	}

	/**
	 * Activate a user account
	 *
	 * @param   string  Encoded Login Column value
	 * @param   string  User's activation code
	 * @return  bool|array
	 * @throws  SentryException
	 */
	public static function activate_user($login_column_value, $code, $decode = true)
	{
		// decode login column
		if ($decode)
		{
			$login_column_value = base64_decode($login_column_value);
		}

		// make sure vars have values
		if (empty($login_column_value) or empty($code))
		{
			return false;
		}

		// if user is validated
		if ($user = static::validate_user($login_column_value, $code, 'activation_hash'))
		{
			// update pass to temp pass, reset temp pass and hash
			$user->update(array(
				'activation_hash' => '',
				'activated' => 1
			), false);

			return $user;
		}

		return false;
	}

	/**
	 * Starts the reset password process.  Generates the necessary password
	 * reset hash and returns the new user array.  Password reset confirm
	 * still needs called.
	 *
	 * @param   string  Login Column value
	 * @param   string  User's new password
	 * @return  bool|array
	 * @throws  SentryException
	 */
	public static function reset_password($login_column_value, $password)
	{
		// make sure a user id is set
		if (empty($login_column_value) or empty($password))
		{
			return false;
		}

		// check if user exists
		$user = static::user($login_column_value);

		// create a hash for reset_password link
		$hash = Str::random(24);

		// set update values
		$update = array(
			'password_reset_hash' => $hash,
			'temp_password' => $password,
			'remember_me' => '',
		);

		// if database was updated return confirmation data
		if ($user->update($update))
		{
			$update = array(
				'email' => $user->get('email'),
				'password_reset_hash' => $hash,
				'link' => base64_encode($login_column_value).'/'.$update['password_reset_hash']
			);

			return $update;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Confirms a password reset code against the database.
	 *
	 * @param   string  Login Column value
	 * @param   string  Reset password code
	 * @return  bool
	 * @throws  SentryException
	 */
	public static function reset_password_confirm($login_column_value, $code, $decode = true)
	{
		// decode login column
		if ($decode)
		{
			$login_column_value = base64_decode($login_column_value);
		}

		// make sure vars have values
		if (empty($login_column_value) or empty($code))
		{
			return false;
		}

		if (static::$suspend)
		{
			// get login attempts
			$attempts = static::attempts($login_column_value, Request::ip());

			// if attempts > limit - suspend the login/ip combo
			if ($attempts->get() >= $attempts->get_limit())
			{
				$attempts->suspend();
			}
		}

		// if user is validated
		if ($user = static::validate_user($login_column_value, $code, 'password_reset_hash'))
		{
			// update pass to temp pass, reset temp pass and hash
			$user->update(array(
				'password' => $user->get('temp_password'),
				'password_reset_hash' => '',
				'temp_password' => '',
				'remember_me' => '',
			), false);

			return true;
		}

		return false;
	}

	/**
	 * Checks if a user exists by Login Column value
	 *
	 * @param   string|id  Login column value or Id
	 * @return  bool
	 */
	public static function user_exists($login_column_value)
	{
		try
		{
			$user = new Sentry_User($login_column_value, true);

			if ($user)
			{
				return true;
			}

			// this should never happen
			return false;
		}
		catch (SentryUserException $e)
		{
			return false;
		}
	}

	/**
	 * Checks if the group exists
	 *
	 * @param   string|int  Group name|Group id
	 * @return  bool
	 */
	public static function group_exists($group)
	{
		try
		{
			$group_exists = new Sentry_Group($group, true);

			if ($group_exists)
			{
				return true;
			}

			// this should never happen
			return false;
		}
		catch(SentryException $e)
		{
			$group_exists = false;
		}
	}

	/**
	 * Remember User Login
	 *
	 * @param int
	 */
	protected static function remember($login_column)
	{
		// generate random string for cookie password
		$cookie_pass = Str::random(24);

		// create and encode string
		$cookie_string = base64_encode($login_column.':'.$cookie_pass);

		// set cookie
		Cookie::put(
			Config::get('sentry::sentry.remember_me.cookie_name'),
			$cookie_string,
			Config::get('sentry::sentry.remember_me.expire')
		);

		return $cookie_pass;
	}

	/**
	 * Check if remember me is set and valid
	 */
	protected static function is_remembered()
	{
		$encoded_val = Cookie::get(Config::get('sentry::sentry.remember_me.cookie_name'));

		if ($encoded_val['value'])
		{
			$val = base64_decode($encoded_val['value']);
			list($login_column, $hash) = explode(':', $val);

			// if user is validated
			if ($user = static::validate_user($login_column, $hash, 'remember_me'))
			{
				// update last login
				$user->update(array(
					'last_login' => time()
				));

				// set session vars
				Session::put(Config::get('sentry::sentry.session.user'), (int) $user->get('id'));
				Session::put(Config::get('sentry::sentry.session.provider'), 'Sentry');

				return true;
			}

			static::logout();
			return false;
		}

		return false;
	}

	/**
	 * Validates a Login and Password.  This takes a password type so it can be
	 * used to validate password reset hashes as well.
	 *
	 * @param   string  Login column value
	 * @param   string  Password to validate with
	 * @param   string  Field name (password type)
	 * @return  bool|Sentry_User
	 */
	protected static function validate_user($login_column_value, $password, $field)
	{
		// get user
		$user = static::user($login_column_value);

		// check activation status
		if ($user->activated != 1 and $field != 'activation_hash')
		{
			throw new SentryException(__('sentry::sentry.account_not_activated'));
		}

		// check user status
		if ($user->status != 1)
		{
			throw new SentryException(__('sentry::sentry.account_is_disabled'));
		}

		// check password
		if ( ! $user->check_password($password, $field))
		{
			if (static::$suspend and ($field == 'password' or $field == 'password_reset_hash'))
			{
				static::attempts($login_column_value, Request::ip())->add();
			}
			return false;
		}

		return $user;
	}

}
