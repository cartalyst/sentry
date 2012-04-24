<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

use Str;

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
