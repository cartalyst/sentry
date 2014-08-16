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

abstract class BaseHasher {

	/**
	 * Compares two strings $a and $b in length-constant time.
	 *
	 * @param  string  $a
	 * @param  string  $b
	 * @return boolean
	 */
	final protected function slowEquals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);

		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}

		return $diff === 0;
	}

}
