<?php namespace Cartalyst\Sentry\Hashing;
/**
 * Part of the Sentry package.
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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class NativeHasher implements HasherInterface {

	/**
	 * {@inheritDoc}
	 */
	public function hash($value)
	{
		if ( ! $hash = password_hash($value, PASSWORD_DEFAULT))
		{
			throw new \RuntimeException('Error hashing value. Check system compatibility with password_hash().');
		}

		return $hash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function check($value, $hashedValue)
	{
		return password_verify($value, $hashedValue);
	}

}
