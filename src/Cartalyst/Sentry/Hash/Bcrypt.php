<?php namespace Cartalyst\Sentry\Hash;

use Cartalyst\Sentry\HashInterface;

class Bcrypt implements HashInterface
{
	/**
	 * Hash Strength
	 *
	 * @var  integer
	 */
	protected $strength = 8;

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
		// format strength
		$strength = str_pad($this->strength, 2, '0', STR_PAD_LEFT);

		// create salt
		$salt = $this->createSalt();

		return crypt($str, '$2a$'.$strength.'$'.$salt);
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
		$strength = substr($hashed_str, 4, 2);

		return crypt($str, $hashed_str) === $hashed_str;
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