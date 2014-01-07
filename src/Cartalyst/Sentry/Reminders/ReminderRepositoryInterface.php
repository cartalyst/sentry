<?php namespace Cartalyst\Sentry\Reminders;
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

use Cartalyst\Sentry\Users\UserInterface;

interface ReminderRepositoryInterface {

	/**
	 * Create a new reminder record and code.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return string
	 */
	public function create(UserInterface $user);

	/**
	 * Check if a valid reminder exists.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function exists(UserInterface $user);

	/**
	 * Complete reminder for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  string  $code
	 * @param  string  $password
	 * @return bool
	 */
	public function complete(UserInterface $user, $code, $password);

	/**
	 * Remove expired reminder codes.
	 *
	 * @return int
	 */
	public function deleteExpired();

}
