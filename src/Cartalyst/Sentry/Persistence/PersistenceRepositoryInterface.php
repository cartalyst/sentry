<?php namespace Cartalyst\Sentry\Persistence;
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

interface PersistenceRepositoryInterface {

	/**
	 * Checks to see if the given user is logged into a session.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function check(UserInterface $user);

	/**
	 * Adds a new user persistence to the current session and attaches the user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function add(UserInterface $user);

	/**
	 * Adds a new user persistence, to remember.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function addAndRemember(UserInterface $user);

	/**
	 * Removes the persistence bound to the current session.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function remove(UserInterface $user);

	/**
	 * Flushes all persistence for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function flush(UserInterface $user);

}
