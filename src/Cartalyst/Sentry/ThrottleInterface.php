<?php namespace Cartalyst\Sentry;

use RuntimeException;
use OutOfBoundsException;

class UserSuspendedException extends RuntimeException {}
class UserBannedException extends RuntimeException {}
class ThrottleLimitException extends OutOfBoundsException {}
class ThrottleTimeException extends OutOfBoundsException {}

interface ThrottleInterface
{
	/**
	 * Set Attempt Limit
	 *
	 * @param   int  $limit
	 */
	public function setAttemptLimit($limit);

	/**
	 * Set Suspension Time
	 *
	 * @param   string  $minutes
	 */
	public function setSuspensionTime($minutes);

	/**
	 * Get Login Attempts
	 *
	 * @param   string  $Login
	 * @return  int
	 */
	public function getAttempts($login);

	/**
	 * Add Login Attempt
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function addAttempt($login);

	/**
	 * Clear Login Attempts
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function clearAttempts($login);

	/**
	 * Suspend a login
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function suspend($login);

	/**
	 * Unsuspend a login
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function unsuspend($login);

	/**
	 * Check if user is suspended
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function isSuspended($login);

	/**
	 * Ban a login
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function ban($login);

	/**
	 * Unban a login
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function unban($login);

	/**
	 * Check if user is banned
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function isBanned($login);

	/**
	 * Check if user throttle status
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function check($login);
}