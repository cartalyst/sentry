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

class Sha256 implements HashInterface {

	/**
	 * Salt Length
	 *
	 * @var integer
	 */
	protected $saltLength = 16;

	/**
	 * Hash String
	 *
	 * @param  string  $str
	 * @return string
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
	 * @param  string  $str
	 * @param  string  $hashedStr
	 * @return bool
	 */
	public function checkHash($str, $hashedStr)
	{
		$salt = substr($hashedStr, 0, 16);

		$password = $salt.hash('sha256', $salt.$str);

		return $password === $hashedStr;
	}

	/**
	 * Create a random string for a salt
	 *
	 * @return string
	 */
	protected function createSalt()
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $this->saltLength);
	}

}