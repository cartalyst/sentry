<?php namespace Cartalyst\Sentry\Checkpoints;
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

interface CheckpointInterface {

	/**
	 * Checkpoint after a user is logged in. Return false to deny persistence.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function login(UserInterface $user);

	/**
	 * Checkpoint for when a user is currently stored in the session.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function check(UserInterface $user);

	/**
	 * Checkpoint for when a failed login attempt is logged. User is not always
	 * passed and the result of the method will not affect anything, as the
	 * login failed.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return void
	 */
	public function fail(UserInterface $user = null);

}
