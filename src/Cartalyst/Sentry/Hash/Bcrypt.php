<?php namespace Cartalyst\Sentry\Hash;
/**
 * Part of the Sentry Package.
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
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

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