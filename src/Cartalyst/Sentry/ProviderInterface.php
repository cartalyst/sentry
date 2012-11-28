<?php namespace Cartalyst\Sentry;

interface ProviderInterface
{
	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function userInterface();

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\GroupInterface
	 */
	public function groupInterface();

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttleInterface();

	/**
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($login, $activationCode);

	/**
	 * Reset a user's password
	 *
	 * @param   string   $login
	 * @param   string   $password
	 * @return  string|false
	 */
	public function resetPassword($login, $password);

	/**
	 * Confirm a password reset request
	 *
	 * @param   string  $login
	 * @param   string  $resetCode
	 * @return  bool
	 */
	public function confirmResetPassword($login, $resetCode);

	/**
	 * Clears Password Reset Fields
	 *
	 * @param   UserInterface  $user
	 * @return  $user
	 */
	public function clearResetPassword(UserInterface $user);

}