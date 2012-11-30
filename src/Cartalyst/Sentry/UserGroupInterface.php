<?php namespace Cartalyst\Sentry;

interface UserGroupInterface
{
	/**
	 * Get user's groups
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function getGroups();

	/**
	 * Add user to group
	 *
	 * @param   int or Cartalyst\Sentry\GroupInterface
	 * @return  bool
	 */
	public function addGroup($group);

	/**
	 * Add user to multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function addGroups(array $groups);

	/**
	 * Remove user from group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function removeGroup($group);

	/**
	 * Remove user from multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function removeGroups(array $groups);

	/**
	 * See if user is in a group
	 *
	 * @param   integer  $group
	 * @return  bool
	 */
	public function inGroup($group);

	/**
	 * Get merged permissions - user overrides groups
	 *
	 * @return  array
	 */
	public function getGroupPermissions();
}