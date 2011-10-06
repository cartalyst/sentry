<?php

/**
 * Sentry Auth class
 *
 * @author  Daniel Petrie
 */

namespace Sentry;

class SentryAuthException extends \Fuel_Exception {}

class Sentry
{
	/**
	 * Prevent instantiation
	 */
	final private function __construct() {}

	public static function user($id = null)
	{
		return new Sentry_User($id);
	}

	/** User Authorization **/
	public static function login($login_id, $password)
	{
		static::check_attempts($login_id);
		// trim and reset vars
		$login_id = trim($login_id);
		$password = trim($password);

		// make sure vars have values
		if (empty($login_id) or empty($password))
		{
			static::add_attempt($login_id);
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
			static::add_attempt($login_id);
			return false;
		}

		// make sure password matches
		if ( ! $user->check_password($password))
		{
			static::add_attempt($login_id);
			return false;
		}

		// load config
		\Config::load('sentry', true);

		// get login_id
		$login_id = \Config::get('sentry.login_id');

		// set session vars
		\Session::set('user', array(
			'id' => $user->get('id'),
			$login_id => $user->get($login_id),
		));

		return true;
	}

	public static function check()
	{

	}

	public static function logout()
	{
		\Session::delete('user');
	}

	public static function check_attempts($login_id)
	{
		$attempts = \Session::get('login_attempts');
		if ( ! empty($attempts) and array_key_exists($login_id, $attempts))
		{
			// if attempts > limit # - suspend username/ip combo
			if ($attempts[$login_id] > 5)
			{
				static::suspend($login_id);
			}
		}
	}

	protected static function add_attempt($login_id)
	{
		$attempts = \Session::get('login_attempts');
		if ( ! empty($attempts) and array_key_exists($login_id, $attempts))
		{
			$attempts[$login_id]++;
		}
		else
		{
			$attempts[$login_id] = 1;
		}
		\Session::set('login_attempts', $attempts);
	}

	public static function clear_attempts()
	{
		\Session::delete('login_attempts');
	}

	protected static function suspend($login_id)
	{
		$result = \DB::insert('users_suspended')->set(array(
			'login_id' => $login_id,
			'ip' => \Input::real_ip(),
			'suspended_at' => time(),
			'unsuspend_at' => time()+60,
		));
	}

}
