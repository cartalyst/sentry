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
	public static function login($login_id, $password) {}

	public static function check() {}

	public static function logout() {}

}
