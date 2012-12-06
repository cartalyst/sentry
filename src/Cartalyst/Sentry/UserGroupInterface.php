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