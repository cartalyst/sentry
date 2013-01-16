<?php namespace Cartalyst\Sentry\Users;
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

use Cartalyst\Sentry\Groups\GroupInterface;

interface UserInterface {

	/**
	 * Returns the user's ID.
	 *
	 * @return  mixed
	 */
	public function getUserId();

	/**
	 * Returns the name for the user's login.
	 *
	 * @return string
	 */
	public function getUserLoginName();

	/**
	 * Returns the user's login.
	 *
	 * @return string
	 */
	public function getUserLogin();

	/**
	 * Returns the user's password (hashed).
	 *
	 * @return string
	 */
	public function getUserPassword();

	/**
	 * Returns permissions for the user.
	 *
	 * @return array
	 */
	public function getUserPermissions();

	/**
	 * Check if user is activated
	 *
	 * @return bool
	 */
	public function isActivated();

	/**
	 * Returns if the user is a super user - has
	 * access to everything regardless of permissions.
	 *
	 * @return void
	 */
	public function isSuperUser();

	/**
	 * Validates the users and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @return bool
	 * @throws Cartalyst\Sentry\Users\LoginRequiredException
	 * @throws Cartalyst\Sentry\Users\UserExistsException
	 */
	public function validate();

	/**
	 * Saves the user.
	 *
	 * @return bool
	 */
	public function save();

	/**
	 * Delete the user.
	 *
	 * @return bool
	 */
	public function delete();

	/**
	 * Get an activation code for the given user.
	 *
	 * @return string
	 */
	public function getActivationCode();

	/**
	 * Attempts to activate the given user by checking
	 * the activate code.
	 *
	 * @param  string  $activationCode
	 * @return bool
	 */
	public function attemptActivation($activationCode);

	/**
	 * Get a reset password code for the given user.
	 *
	 * @return string
	 */
	public function getResetPasswordCode();

	/**
	 * Attemps to reset a user's password by matching
	 * the reset code generated with the user's.
	 *
	 * @param  string  $resetCode
	 * @param  string  $newPassword
	 * @return bool
	 */
	public function attemptResetPassword($resetCode, $newPassword);

	/**
	 * Wipes out the data associated with resetting
	 * a password.
	 *
	 * @return void
	 */
	public function clearResetPassword();

	/**
	 * Returns an arrya of groups which the given
	 * user belongs to.
	 *
	 * @return array
	 */
	public function getGroups();

	/**
	 * Adds the user to the given group
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return void
	 */
	public function addGroup(GroupInterface $group);

	/**
	 * Remove user from the given group.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function removeGroup(GroupInterface $group);

	/**
	 * See if user is in the given group.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function inGroup(GroupInterface $group);

	/**
	 * Returns an array of merged permissions for each
	 * group the user is in.
	 *
	 * @return array
	 */
	public function getMergedPermissions();

	/**
	 * See if a user has a required permission. Permissions
	 * are merged from all groups the user belongs to
	 * and then are checked against the passed permission.
	 *
	 * @param  string  $permission
	 * @return bool
	 */
	public function hasAccess($permission);

}