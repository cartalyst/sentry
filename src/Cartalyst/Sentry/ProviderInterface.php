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

use OutOfBoundsException;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\GroupInterface;

class InvalidObjectException extends OutOfBoundsException {}

interface ProviderInterface
{
	/**
	 * Get the user interface
	 *
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function userInterface();

	/**
	 * Get the group interface
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function groupInterface();

	/**
	 * Get the throttle interface
	 *
	 * @return Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttleInterface();

	/**
	 * Registers a user with activation code
	 *
	 * @return string
	 */
	public function registerUser(array $attributes);

	/**
	 * Creates a user
	 *
	 * @return string
	 */
	public function createUser(array $attributes);

	/**
	 * Creates a Group
	 *
	 * @return bool
	 */
	public function createGroup(array $attributes);

	/**
	 * Saves a user object
	 *
	 * @return bool
	 */
	public function saveUser(UserInterface $user);

	/**
	 * Saves a group object
	 *
	 * @return bool
	 */
	public function saveGroup(GroupInterface $group);

	/**
	 * Deletes a user object
	 *
	 * @return bool
	 */
	public function deleteUser(UserInterface $user);

	/**
	 * Deletes a user object
	 *
	 * @return bool
	 */
	public function deleteGroup(GroupInterface $group);

}