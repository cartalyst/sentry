<?php namespace Cartalyst\Sentry;
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

use RuntimeException;
use OutOfBoundsException;

class UserSuspendedException extends RuntimeException {}
class UserBannedException extends RuntimeException {}
class ThrottleLimitException extends OutOfBoundsException {}
class ThrottleTimeException extends OutOfBoundsException {}

interface ThrottleInterface {

	/**
	 * Set Attempt Limit
	 *
	 * @param  int  $limit
	 */
	public function setAttemptLimit($limit);

	/**
	 * Set Suspension Time
	 *
	 * @param  string  $minutes
	 */
	public function setSuspensionTime($minutes);

	/**
	 * Get Login Attempts
	 *
	 * @param  string  $Login
	 * @return int
	 */
	public function getAttempts($login);

	/**
	 * Add Login Attempt
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function addAttempt($login);

	/**
	 * Clear Login Attempts
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function clearAttempts($login);

	/**
	 * Suspend a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function suspend($login);

	/**
	 * Unsuspend a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function unsuspend($login);

	/**
	 * Check if user is suspended
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function isSuspended($login);

	/**
	 * Ban a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function ban($login);

	/**
	 * Unban a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function unban($login);

	/**
	 * Check if user is banned
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function isBanned($login);

	/**
	 * Check if user throttle status
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function check($login);

}