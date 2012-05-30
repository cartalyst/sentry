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

use Str;

/**
 * Default Sentry Hashing Driver
 */
class Sentry_Hash_Strategy_Sentry extends Sentry_Hash_Driver
{

	/**
	 * set constructor
	 */
	public function __construct($options) {}

	/**
	 * Creates a random salt and hashes the given password with the salt.
	 * String returned is prepended with a 16 character alpha-numeric salt.
	 *
	 * @param   string  Password to generate hash/salt for
	 * @return  string
	 */
	public function create_password($password)
	{
		$salt = Str::random(16);

		return $salt.$this->hash_password($password, $salt);
	}

	/**
	 * Checks the given password to see if it matches the one in the database.
	 *
	 * @param   string  Password to check
	 * @param   string  Hashed User Password
	 * @return  bool
	 */
	public function check_password($password, $hashed_password)
	{
		// grabs the salt from the current password
		$salt = substr($hashed_password, 0, 16);

		// hash the inputted password
		$password = $salt.$this->hash_password($password, $salt);

		// check to see if passwords match
		return $password == $hashed_password;
	}

	/**
	 * Hash a given password with the given salt.
	 *
	 * @param   string  Password to hash
	 * @param   string  Password Salt
	 * @return  string
	 */
	protected function hash_password($password, $salt)
	{
		$password = hash('sha256', $salt.$password);

		return $password;
	}

}
