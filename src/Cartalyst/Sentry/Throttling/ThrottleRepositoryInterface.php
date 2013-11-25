<?php namespace Cartalyst\Sentry\Throttling;
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

use Cartalyst\Sentry\Users\UserInterface;

interface ThrottleRepositoryInterface {

	/**
	 * Returns the global throttling delay, in seconds.
	 *
	 * @return int
	 */
	public function globalDelay();

	/**
	 * Returns the IP address throttling delay, in seconds.
	 *
	 * @param  string  $ipAddress
	 * @return int
	 */
	public function ipDelay($ipAddress);

	/**
	 * Returns the throttling delay for the given user, in seconds.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return int
	 */
	public function userDelay(UserInterface $user);

	/**
	 * Log a new throttling entry.
	 *
	 * @param  string  $ipAddress
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return void
	 */
	public function log($ipAddress, UserInterface $user = null);

}
