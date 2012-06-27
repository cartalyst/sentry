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
 * OSCommerce Hashing Driver
 */
class Sentry_Hash_Strategy_Oscommerce extends Sentry_Hash_Driver
{

	public function __construct($options)
	{
		$this->options = $options;
	}

	public function create_password($password)
	{
		$salt = false;
		if (is_integer($this->options['salt']))
		{
			$salt = Str::random($this->options['salt']);
		}

		return $this->hash_password($password, $salt);
	}

	public function check_password($password, $hashed_password)
	{
		$hashArr = explode(':', $hashed_password);
		switch (count($hashArr))
		{
			case 1:
				return $this->hash_password($password) === $hashed_password;
			case 2:
				return $this->hash_password($hashArr[1].$password) === $hashArr[0];
		}

		// check to see if passwords match
		return $password == $hashed_password;
	}

	protected function hash_password($password, $salt = false)
	{
		return $salt === false ? md5($password) : md5($salt.$password).':'.$salt;
	}

}
