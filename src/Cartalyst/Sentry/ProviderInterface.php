<?php namespace Cartalyst\Sentry;

use OutOfBoundsException;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\GroupInterface;

class InvalidObjectException extends OutOfBoundsException {}

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
	 * Registers a user with activation code
	 *
	 * @return  string
	 */
	public function registerUser(array $attributes);

	/**
	 * Creates a user
	 *
	 * @return  string
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
	 * @return  bool
	 */
	public function saveUser(UserInterface $user);

	/**
	 * Saves a group object
	 *
	 * @return  bool
	 */
	public function saveGroup(GroupInterface $group);

	/**
	 * Deletes a user object
	 *
	 * @return  bool
	 */
	public function deleteUser(UserInterface $user);

	/**
	 * Deletes a user object
	 *
	 * @return  bool
	 */
	public function deleteGroup(GroupInterface $group);

}