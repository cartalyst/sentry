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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class BcryptHasher extends BaseHasher implements HasherInterface {

	/**
	 * Hash strength.
	 *
	 * @var int
	 */
	public $strength = 8;

	/**
	 * {@inheritDoc}
	 */
	public function hash($value)
	{
		// Format strength
		$strength = str_pad($this->strength, 2, '0', STR_PAD_LEFT);

		// Create salt
		$salt = $this->createSalt();

		//create prefix; $2y$ fixes blowfish weakness
		$prefix = PHP_VERSION_ID < 50307 ? '$2a$' : '$2y$';

		return crypt($value, $prefix.$strength.'$'.$salt.'$');
	}

	/**
	 * {@inheritDoc}
	 */
	public function checkhash($value, $hashedValue)
	{
		return crypt($value, $hashedValue) === $hashedValue;
	}

}
