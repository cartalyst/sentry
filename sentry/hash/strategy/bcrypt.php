<?php
/**
 * Part of the Sentry package for Laravel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

 namespace Sentry;

use Str;

/**
 * BCrypt Hashing Driver
 */
class Sentry_Hash_Strategy_BCrypt extends Sentry_Hash_Driver
{

	public function __construct($options)
	{
		if ( ! isset($options['hashing_algorithm']))
		{
			$options['hashing_algorithm'] = null;
		}

		if ( ! isset($options['strength']) or $options['strength'] < 4 or $options['strength'] > 31)
		{
			$options['strength'] = 8;
		}

		$this->options = $options;
	}

	public function create_password($password)
	{
		$salt = '';

		if ($this->options['hashing_algorithm'])
		{
			$salt = Str::random(16);
		}
		else
		{
			for ($i = 0; $i < 22; $i++)
			{
				$salt .= substr("./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 63), 1);
			}
		}

		return $this->hash_password($password, $salt);
	}

	public function check_password($password, $hashed_password)
	{
		if ($this->options['hashing_algorithm'] == 'hash')
		{
			$salt = substr($hashed_password, 0, 16);

			$password = $salt.$this->hash_password($password, $salt);

			return $password == $hashed_password;
		}

		$strength = substr($hashed_password, 4, 2);

		return (substr($hashed_password, 0, 60) === crypt($password, "$2a$".$strength."$".substr($hashed_password, 60)));
	}

	protected function hash_password($password, $salt)
	{
		if ($this->options['hashing_algorithm'])
		{
			return $salt.hash($this->options['hashing_algorithm'], $salt.$password);
		}

		$strength = $this->options['strength'];

		return crypt($password, sprintf('$2a$%02d$', $strength).$salt).$salt;

	}

}
