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

class UserNotActivatedException extends \RuntimeException {}
class UserNotFoundException extends \RuntimeException {}

interface ProviderInterface {

	/**
	 * Get user login column
	 *
	 * @return string
	 */
	public function getLoginColumn();

	/**
	 * Get user login column
	 *
	 * @param  int  $id
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findById($id);

	/**
	 * Get user by login value
	 *
	 * @param  string  $login
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login);

	/**
	 * Get user by credentials
	 *
	 * @param  array  $credentials
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials(array $attributes);

	/**
	 * Activate a user
	 *
	 * @param  string  $login
	 * @param  string  $activationCode
	 * @return bool
	 */
	public function activate($activationCode);

	/**
	 * Check if user is activated
	 *
	 * @param  UserInterface  $user
	 * @return bool
	 */
	public function isActivated();

	/**
	 * Reset a user's password
	 *
	 * @return string|false
	 */
	public function resetPassword();

	/**
	 * Confirm a password reset request
	 *
	 * @param  string  $password
	 * @param  string  $resetCode
	 * @return bool
	 */
	public function resetPasswordConfirm($password, $resetCode);

	/**
	 * Clears Password Reset Fields
	 *
	 * @param  UserInterface  $user
	 * @return $user
	 */
	public function clearResetPassword();

	/**
	 * Get user's groups
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function getGroups();

	/**
	 * Add user to group
	 *
	 * @param  int or Cartalyst\Sentry\GroupInterface
	 * @return bool
	 */
	public function addGroup($group);

	/**
	 * Remove user from group
	 *
	 * @param  int|Cartalyst\Sentry\GroupInterface  $group
	 * @return bool
	 */
	public function removeGroup($group);

	/**
	 * See if user is in a group
	 *
	 * @param  int  $group
	 * @return bool
	 */
	public function inGroup($group);

	/**
	 * Get merged permissions - user overrides groups
	 *
	 * @return array
	 */
	public function getGroupPermissions();

	/**
	 * See if a user has a required permission
	 *
	 * @param  string  $permission
	 * @return bool
	 */
	public function hasAccess($permission);

}