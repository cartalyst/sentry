<?php namespace Cartalyst\Sentry\Hash;

use Cartalyst\Sentry\HashInterface;

class Sha256 implements HashInterface
{
	/**
	 * Salt Length
	 *
	 * @var  integer
	 */
	protected $saltLength = 16;

	/**
	 * Hash String
	 *
	 * @param   string  $str
	 * @return  string
	 */
	public function hash($str)
	{
		// create salt
		$salt = $this->createSalt();

		return $salt.hash('sha256', $salt.$password);
	}

	/**
	 * Check Hash Values
	 *
	 * @param   string  $str
	 * @param   string  $hashed_str
	 * @return  bool
	 */
	public function checkHash($str, $hashed_str)
	{
		$salt = substr($hashed_str, 0, 16);

		$password = $salt.hash('sha256', $salt.$str);

		return $password === $hashed_str;
	}

	/**
	 * Create a random string for a salt
	 *
	 * @return  string
	 */
	protected function createSalt()
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $this->saltLength);
	}
}