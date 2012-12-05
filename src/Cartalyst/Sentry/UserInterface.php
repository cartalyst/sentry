<?php namespace Cartalyst\Sentry;

use RuntimeException;

class UserExistsException extends RuntimeException {}
class UserNotActivatedException extends RuntimeException {}
class LoginFieldRequiredException extends RuntimeException {}
class UserNotFoundException extends RuntimeException {}

interface UserInterface
{
	/**
	 * Get user login column
	 *
	 * @return  string
	 */
	public function getLoginColumn();

	/**
	 * Get user login column
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findById($id);

	/**
	 * Get user by login value
	 *
	 * @param   string  $login
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login);

	/**
	 * Get user by credentials
	 *
	 * @param   array  $credentials
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials(array $attributes);

	/**
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($activationCode);

	/**
	 * Check if user is activated
	 *
	 * @param   UserInterface  $user
	 * @return  bool
	 */
	public function isActivated();

	/**
	 * Reset a user's password
	 *
	 * @return  string|false
	 */
	public function resetPassword();

	/**
	 * Confirm a password reset request
	 *
	 * @param   string  $password
	 * @param   string  $resetCode
	 * @return  bool
	 */
	public function resetPasswordConfirm($password, $resetCode);

	/**
	 * Clears Password Reset Fields
	 *
	 * @param   UserInterface  $user
	 * @return  $user
	 */
	public function clearResetPassword();

	/**
	 * See if a user has a required permission
	 *
	 * @param   string  $permission
	 * @return  bool
	 */
	public function hasAccess($permission);

}