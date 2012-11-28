<?php namespace Cartalyst\Sentry\Provider;

use Cartalyst\Sentry\ProviderInterface;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\Model\User;
use Cartalyst\Sentry\Model\Group;
use Cartalyst\Sentry\Model\Throttle;

class Eloquent implements ProviderInterface
{
	/**
	 * The user interface
	 *
	 * @var  Cartalyst\Sentry\UserInterface
	 */
	protected $userInterface;

	/**
	 * The group interface
	 *
	 * @var  Cartalyst\Sentry\GroupInterface
	 */
	protected $groupInterface;

	/**
	 * The throttle interface
	 *
	 * @var  Cartalyst\Sentry\ThrottleInterface
	 */
	protected $throttleInterface;

	/**
	 * Constructor
	 *
	 * @return  Cartalyst\Sentry\ProviderInterface
	 */
	public function __construct()
	{
		$this->userInterface = new User();
		$this->groupInterface = new Group();
		$this->throttleInterface = new Throttle();
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function userInterface()
	{
		return $this->userInterface;
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\GroupInterface
	 */
	public function groupInterface()
	{
		return $this->groupInterface;
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttleInterface()
	{
		return $this->throttleInterface;
	}

	/**
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($login, $activationCode)
	{
		$user = $this->userInterface->findByLogin($login);

		if ($user and $activationCode === $user->activation_hash)
		{
			$user->activation_hash = null;
			$user->activated = 1;
			$user->save();

			return true;
		}

		return false;
	}

	/**
	 * Reset a user's password
	 *
	 * @param   string   $login
	 * @param   string   $password
	 * @return  string|false
	 */
	public function resetPassword($login, $password)
	{
		$user = $this->userInterface->findByLogin($login);

		if ($user)
		{
			$resetCode = $this->randomString();

			$user->temp_password = $password;
			$user->reset_password_hash = $resetCode;
			$user->save();

			return $resetCode;
		}

		return false;
	}

	/**
	 * Confirm a password reset request
	 *
	 * @param   string  $login
	 * @param   string  $resetCode
	 * @return  bool
	 */
	public function confirmResetPassword($login, $resetCode)
	{
		$user = $this->userInterface->findByLogin($login);

		if ($user and $resetCode === $user->reset_password_hash)
		{
			$user->password = $user->temp_password;
			$user->temp_password = null;
			$user->reset_password_hash = null;
			$user->save();

			return true;
		}

		return false;
	}

	/**
	 * Clears Password Reset Fields
	 *
	 * @param   UserInterface  $user
	 * @return  $user
	 */
	public function clearResetPassword(UserInterface $user)
	{
		if ($user->temp_password or $user->reset_password_hash)
		{
			$user->temp_password = null;
			$user->reset_password_hash = null;
			$user->save();
		}

		return $user;
	}

	/**
	 * Generate a random string
	 *
	 * @return  string
	 */
	protected function randomString()
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, 40);
	}

}