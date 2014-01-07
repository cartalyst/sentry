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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

abstract class BasePermissions implements PermissionsInterface {

	/**
	 * Permissions.
	 *
	 * @var array
	 */
	protected $permissions = array();

	/**
	 * Secondary permissions.
	 *
	 * @var array
	 */
	protected $secondaryPermissions = array();

	/**
	 * An array of cached, prepared permissions.
	 *
	 * @var array
	 */
	protected $preparedPermissions;

	/**
	 * Create a new permissions instance.
	 *
	 * @param  array  $permissions
	 * @param  array  $secondaryPermissions
	 * @return void
	 */
	public function __construct(array $permissions = null, array $secondaryPermissions = null)
	{
		if (isset($permissions))
		{
			$this->permissions = $permissions;
		}

		if (isset($secondaryPermissions))
		{
			$this->secondaryPermissions = $secondaryPermissions;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccess($permissions)
	{
		$prepared = $this->getPreparedPermissions();

		foreach ((array) $permissions as $permission)
		{
			if ( ! $this->checkPermission($prepared, $permission))
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
		$prepared = $this->getPreparedPermissions();

		foreach ((array) $permissions as $permission)
		{
			if ($this->checkPermission($prepared, $permission))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get permissions.
	 *
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Set permissions.
	 *
	 * @param  array  $permissions
	 * @return void
	 */
	public function setPermissions(array $permissions)
	{
		$this->permissions = $permissions;
		$this->preparedPermissions = null;
	}

	/**
	 * Get secondary permissions.
	 *
	 * @return array
	 */
	public function getSecondaryPermissions()
	{
		return $this->secondaryPermissions;
	}

	/**
	 * Set secondary permissions.
	 *
	 * @param  array  $secondaryPermissions
	 * @return void
	 */
	public function setSecondaryPermissions(array $secondaryPermissions)
	{
		$this->secondaryPermissions = $secondaryPermissions;
		$this->preparedPermissions = null;
	}

	/**
	 * Lazily grabs prepared permissions.
	 *
	 * @return array
	 */
	protected function getPreparedPermissions()
	{
		if ($this->preparedPermissions === null)
		{
			$this->preparedPermissions = $this->createPreparedPermissions();
		}

		return $this->preparedPermissions;
	}

	/**
	 * Does the heavy lifting of preparing permissions.
	 *
	 * @param  array  $prepared
	 * @param  array  $permissions
	 * @return void
	 */
	protected function preparePermissions(array &$prepared, array $permissions)
	{
		foreach ($permissions as $keys => $value)
		{
			foreach ($this->extractClassPermissions($keys) as $key)
			{
				// If the value is not in the array, we're opting in
				if ( ! array_key_exists($key, $prepared))
				{
					$prepared[$key] = $value;
					continue;
				}

				// If our value is in the array and equals false, it will override
				if ($value === false)
				{
					$prepared[$key] = $value;
				}
			}
		}
	}

	/**
	 * Takes the given permission key and inspects it for a class & method. If
	 * it exists, methods may be comma-separated, e.g. Class@method1,method2.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function extractClassPermissions($key)
	{
		if ( ! str_contains($key, '@'))
		{
			return (array) $key;
		}

		$keys = array();

		list($class, $methods) = explode('@', $key);

		foreach (explode(',', $methods) as $method)
		{
			$keys[] = "{$class}@{$method}";
		}

		return $keys;
	}

	/**
	 * Checks a permission in the prepared array, including wildcard permissions.
	 *
	 * @param  array   $prepared
	 * @param  string  $permission
	 * @return bool
	 */
	protected function checkPermission(array $prepared, $permission)
	{
		if (array_key_exists($permission, $prepared) and $prepared[$permission] === true)
		{
			return true;
		}

		foreach ($prepared as $key => $value)
		{
			if (str_is($permission, $key) and $value === true)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns prepared permissions.
	 *
	 * @return void
	 */
	abstract protected function createPreparedPermissions();

}
