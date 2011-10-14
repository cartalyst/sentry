<?php

/**
 * Part of the Sentry package for Fuel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

/**
 * Sentry Auth class
 *
 * @author  Daniel Petrie
 */

class SentryAuthException extends \Fuel_Exception {}

class SentryAuthConfigException extends \SentryAuthException {}

class Sentry
{
	protected static $login_id = null;

	protected static $attempts = null;

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
		static::$attempts = new Sentry_Attempts();

		// validate config settings

		// login_id check
		if (static::$login_id != 'username' and static::$login_id != 'email')
		{
			throw new \SentryAuthConfigException(
				'Sentry Config Item: "login_id" must be set to "username" or "email".');
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
	public static function login($login_id, $password, $remember = false)
	{
		// log the user out if they hit the login page
		static::logout();

		// get login attempts
		$attempts = static::$attempts->get($login_id);

		// if attempts > limit - suspend the login/ip combo
		if ($attempts >= static::$attempts->get_limit())
		{
			try
			{
				static::$attempts->suspend($login_id);
			}
			catch(SentryUserSuspendedException $e)
			{
				throw new \SentryAuthException($e->getMessage());
			}
		}

		// make sure vars have values
		if (empty($login_id) or empty($password))
		{
			return false;
		}

		// if user is validated
		if ($user = static::validate_user($login_id, $password, 'password'))
		{
			// clear attempts for login since they got in
			static::$attempts->clear($login_id);

			// set update array
			$update = array();

			// if they wish to be remembers, set the cookie and get the hash
			if ($remember)
			{
				$update['remember_me'] = static::remember($login_id);
			}

			// if there is a password reset hash and user logs in - remove the password reset
			if ($user->get('password_reset_hash'))
			{
				$update['password_reset_hash'] = '';
				$update['temp_password'] = '';
			}

			$update['last_login'] = time();

			// update user
			if (count($update))
			{
				$user->update($update, false);
			}

			// set session vars
			\Session::set('sentry_user', array(
				'id' => (int) $user->get('id')
			));

			return true;
		}

		return false;
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
	 * Log current user out
	 */
	public static function logout()
	{
		\Cookie::delete(\Config::get('sentry.remember_me.cookie_name'));
		\Session::delete('sentry_user');
	}

	/**
	 * Forgot Password
	 *
	 * @param string
	 * @param string
	 */
	public static function forgot_password($login_id, $password)
	{
		// make sure a user id is set
		if (empty($login_id) or empty($password))
		{
			throw new \SentryAuthException(
				'Username and Password must be set to use forgot password.');
		}

		// check if user exists
		try
		{
			// get user from database
			$user = new Sentry_User($login_id);
		}
		catch (SentryUserNotFoundException $e)
		{
			throw new \SentryAuthException('User does not exist.');
		}

		// create a hash for forgot_password link
		$hash = \Str::random('alnum', 24);

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
				'login_id' => $login_id,
				'link' => base64_encode($login_id).'/'.$update['password_reset_hash']
			) + $update;

			return $update;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Forgot Password Confirmation Check
	 *
	 * @param string
	 */
	public static function forgot_password_confirm($login_id, $code)
	{
		$login_id = base64_decode($login_id);

		// get login attempts
		$attempts = static::$attempts->get($login_id);

		// if attempts > limit - suspend the login/ip combo
		if ($attempts >= static::$attempts->get_limit())
		{
			static::$attempts->suspend($login_id);
		}

		// make sure vars have values
		if (empty($login_id) or empty($code))
		{
			return false;
		}

		// if user is validated
		if ($user = static::validate_user($login_id, $code, 'password_reset_hash'))
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
	 * Remember User Login
	 *
	 * @param int
	 */
	protected static function remember($login_id)
	{
		// generate random string for cookie password
		$cookie_pass = \Str::random('alnum', 24);

		// create and encode string
		$cookie_string = base64_encode($login_id.':'.$cookie_pass);

		// set cookie
		\Cookie::set(
			\Config::get('sentry.remember_me.cookie_name'),
			$cookie_string,
			\Config::get('sentry.remember_me.expire')
		);

		return $cookie_pass;
	}

	/**
	 * Check if remember me is set and valid
	 */
	protected static function is_remembered()
	{
		$encoded_val = \Cookie::get(\Config::get('sentry.remember_me.cookie_name'));

		if ($encoded_val)
		{
			$val = base64_decode($encoded_val);
			list($login_id, $hash) = explode(':', $val);

			// if user is validated
			if ($user = static::validate_user($login_id, $hash, 'remember_me'))
			{
				// update last login
				$user->update(array(
					'last_login' => time()
				));

				// set session vars
				\Session::set('sentry_user', array(
					'id' => (int) $user->get('id')
				));

				return true;
			}
			else
			{
				static::logout();
				return false;
			}
		}

		return false;
	}

	protected static function validate_user($login_id, $password, $field)
	{
		// get user
		try
		{
			// get user from database
			$user = new Sentry_User($login_id);
		}
		catch (SentryUserNotFoundException $e)
		{
			// user not found
			return false;
		}

		if ( ! $user->check_password($password, $field))
		{
			if ($field == 'password' or $field == 'password_reset_hash')
			{
				static::$attempts->add($login_id);
			}
			return false;
		}

		return $user;
	}

}
