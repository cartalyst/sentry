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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Closure;

class CallbackHasher implements HasherInterface {

	/**
	 * The closure used for hashing a value.
	 *
	 * @var \Closure
	 */
	protected $hash;

	/**
	 * The closure used for checking a hashed value.
	 *
	 * @var \Closure
	 */
	protected $checkHash;

	/**
	 * Create a new callback hasher instance.
	 *
	 * @param  \Closure  $hash
	 * @param  \Closure  $checkHash
	 */
	public function __construct(Closure $hash, Closure $checkHash)
	{
		$this->hash = $hash;
		$this->checkHash = $checkHash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hash($value)
	{
		return $this->$hash($value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function checkhash($value, $hashedValue)
	{
		return $this->$checkHash($value, $hashedValue);
	}

}
