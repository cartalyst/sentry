<?php namespace Cartalyst\Sentry\Groups\Eloquent;
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

use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Groups\NameFieldRequiredException;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Illuminate\Database\Eloquent\Model;

class Group extends Model implements GroupInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'groups';

	/**
	 * Allowed permissions values.
	 *
	 * Possible options:
	 *    0 => Delete permissions
	 *    1 => Add permissions
	 *
	 * @var array
	 */
	protected $allowedPermissionsValues = array(0, 1);

	/**
	 * Returns the group's ID
	 *
	 * @return mixed
	 */
	public function getGroupId()
	{
		return $this->getKey();
	}

	/**
	 * Returns the group's name
	 *
	 * @return mixed
	 */
	public function getGroupName()
	{
		return $this->name;
	}

	/**
	 * Validates the group and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @return bool
	 * @throws Cartalyst\Sentry\Groups\NameFieldRequiredException
	 * @throws Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function validate()
	{
		// Check if name field was passed
		if (empty($this->name))
		{
			throw new NameFieldRequiredException;
		}

		// Check if group already exists
		try
		{
			$group = $this->findByName($this->name);
		}
		catch (GroupNotFoundException $e)
		{
			$group = null;
		}

		if ($group and $group->getKey() != $this->getKey())
		{
			throw new GroupExistsException;
		}

		return true;
	}

	/**
	 * Saves the given group.
	 *
	 * @return bool
	 */
	public function save()
	{
		$this->validate();
		return parent::save();
	}

	/**
	 * Get user specific permissions
	 *
	 * @param  string  $permissions
	 * @return array
	 */
	public function getPermissions($permissions)
	{
		return ( ! is_null($permissions)) ? json_decode($permissions, true) : array();
	}

	/**
	 * Set user specific permissions
	 *
	 * @param  array  $permissions
	 * @return string
	 */
	public function setPermissions(array $permissions)
	{
		// Merge permissions
		$permissions = array_merge((array) $this->permissions, $permissions);

		// Loop through and adjsut permissions as needed
		foreach ($permissions as $permission => $value)
		{
			// Lets make sure their is a valid permission value
			if ( ! in_array($value, $this->allowedPermissionsValues, true))
			{
				throw new \InvalidArgumentException("Invalid value [$value] for permission [$permission] given.");
			}

			// If the value is 0, delete it
			if ($value === 0)
			{
				unset($permissions[$permission]);
			}
		}

		return json_encode($permissions);
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$attributes = parent::toArray();

		if (isset($attributes['permissions']))
		{
			$attributes['permissions'] = json_decode($attributes['permissions'], true);
		}

		return $attributes;
	}

}