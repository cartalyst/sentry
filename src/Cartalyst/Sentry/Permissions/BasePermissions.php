<?php namespace Cartalyst\Sentry\Permissions;
/**
 * Part of the Sentry package.
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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

abstract class BasePermissions implements PermissionsInterface {

	/**
	 * User permissions.
	 *
	 * @var array
	 */
	protected $userPermissions = array();

	/**
	 * Group permissions (where each item is a group containing permissions).
	 *
	 * @var array
	 */
	protected $groupPermissions = array();

	/**
	 * {@inheritDoc}
	 */
	public function hasAccess($permissions)
	{
		$merged = $this->getMergedPermissions();

		foreach ((array) $permissions as $permission)
		{
			if ( ! $this->checkPermission($merged, $permission))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAnyAccess($permissions)
	{
		$merged = $this->getMergedPermissions();

		foreach ((array) $permissions as $permission)
		{
			if ($this->checkPermission($merged, $permission))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get user permissions.
	 *
	 * @return array
	 */
	public function getUserPermissions()
	{
		return $this->userPermissions;
	}

	/**
	 * Set user permissions.
	 *
	 * @param  array  $userPermissions
	 * @return void
	 */
	public function setUserPermissions(array $userPermissions)
	{
		$this->userPermissions = $userPermissions;
	}

	/**
	 * Get group permissions.
	 *
	 * @return array
	 */
	public function getGroupPermissions()
	{
		return $this->groupPermissions;
	}

	/**
	 * Set group permissions.
	 *
	 * @param  array  $groupPermissions
	 * @return void
	 */
	public function setGroupPermissions(array $groupPermissions)
	{
		$this->groupPermissions = $groupPermissions;
	}

	/**
	 * Returns merged permissions.
	 *
	 * @return void
	 */
	protected function getMergedPermissions()
	{
		$merged = array();

		if ( ! empty($this->groupPermissions))
		{
			foreach ($this->groupPermissions as $permissions)
			{
				$this->mergePermissions($merged, $permissions);
			}
		}

		if ( ! empty($this->userPermissions))
		{
			$this->mergePermissions($merged, $this->userPermissions);
		}

		return $merged;
	}

	/**
	 * Does the heavy lifting of merging permissions.
	 *
	 * @param  array  $merged
	 * @param  array  $permissions
	 * @return void
	 */
	protected function mergePermissions(array &$merged, array $permissions)
	{
		foreach ($permissions as $key => $value)
		{
			// If the value is not in the array, we're opting in
			if ( ! array_key_exists($key, $merged))
			{
				$merged[$key] = $value;
				continue;
			}

			// If our value is in the array and equals false, it will override
			if ($value === false)
			{
				$merged[$key] = $value;
			}
		}
	}

	/**
	 * Checks a permission in the merged array, including wildcard permissions.
	 *
	 * @param  array   $merged
	 * @param  string  $permission
	 * @return bool
	 */
	protected function checkPermission(array $merged, $permission)
	{
		if (array_key_exists($permission, $merged) and $merged[$permission] === true)
		{
			return true;
		}

		foreach ($merged as $key => $value)
		{
			if (str_is($permission, $key) and $value === true)
			{
				return true;
			}
		}

		return false;
	}

}
