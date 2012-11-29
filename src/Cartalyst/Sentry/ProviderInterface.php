<?php namespace Cartalyst\Sentry;

interface ProviderInterface
{
	/**
	 * Get the user interface
	 *
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function userInterface();

	/**
	 * Get the group interface
	 *
	 * @return  Cartalyst\Sentry\GroupInterface
	 */
	public function groupInterface();

	/**
	 * Get the throttle interface
	 *
	 * @return  Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttleInterface();

	/**
	 * Get the hash interface
	 *
	 * @return  Cartalyst\Sentry\ThrottleInterface
	 */
	public function hashInterface();

	/**
	 * Registers a user
	 *
	 * @return
	 */
	public function register(array $attributes);

	/**
	 * Registers a user
	 *
	 * @return
	 */
	public function save(UserInterface $user);

	/**
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($login, $activationCode);

	/**
	 * Check if user is activated
	 *
	 * @param   UserInterface  $user
	 * @return  bool
	 */
	public function isActivated(UserInterface $user);

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
	public function ResetPasswordConfirm($login, $resetCode);

	/**
	 * Clears Password Reset Fields
	 *
	 * @param   UserInterface  $user
	 * @return  $user
	 */
	public function clearResetPassword(UserInterface $user);

	/**
	 * Hash String
	 *
	 * @param   string  $str
	 * @return  string
	 */
	public function hash($str);

	/**
	 * Check Hash Values
	 *
	 * @param   string  $str
	 * @param   string  $hashed_str
	 * @return  bool
	 */
	public function checkHash($str, $hashed_str);

}