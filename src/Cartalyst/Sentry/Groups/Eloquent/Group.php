<?php namespace Cartalyst\Sentry\Groups\Eloquent;
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

use Cartalyst\Sentry\Groups\NameRequiredException;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupInterface;
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
	 *    0 => Remove.
	 *    1 => Add.
	 *
	 * @var array
	 */
	protected $allowedPermissionsValues = array(0, 1);

	/**
	 * Returns the group's ID.
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->getKey();
	}

	/**
	 * Returns the group's name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns permissions for the group.
	 *
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Returns the relationship between groups and users.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany('Cartalyst\Sentry\Users\Eloquent\User', 'users_groups');
	}

	/**
	 * Saves the group.
	 *
	 * @return bool
	 */
	public function save()
	{
		$this->validate();
		return parent::save();
	}

	/**
	 * Delete the group.
	 *
	 * @return bool
	 */
	public function delete()
	{
		$this->users()->detach();
		return parent::delete();
	}

	/**
	 * Mutator for giving permissions.
	 *
	 * @param  mixed  $permissions
	 * @return array  $_permissions
	 */
	public function getPermissionsAttribute($permissions)
	{
		if ( ! $permissions)
		{
			return array();
		}

		if (is_array($permissions))
		{
			return $permissions;
		}

		if ( ! $_permissions = json_decode($permissions, true))
		{
			throw new \InvalidArgumentException("Cannot JSON decode permissions [$permissions].");
		}

		return $_permissions;
	}

	/**
	 * Mutator for taking permissions.
	 *
	 * @param  array  $permissions
	 * @return void
	 */
	public function setPermissionsAttribute(array $permissions)
	{
		// Merge permissions
		$permissions = array_merge($this->getPermissions(), $permissions);

		// Loop through and adjsut permissions as needed
		foreach ($permissions as $permission => &$value)
		{
			// Lets make sure their is a valid permission value
			if ( ! in_array($value = (int) $value, $this->allowedPermissionsValues))
			{
				throw new \InvalidArgumentException("Invalid value [$value] for permission [$permission] given.");
			}

			// If the value is 0, delete it
			if ($value === 0)
			{
				unset($permissions[$permission]);
			}
		}

		$this->attributes['permissions'] = ( ! empty($permissions)) ? json_encode($permissions) : '';
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
			$attributes['permissions'] = $this->getPermissionsAttribute($attributes['permissions']);
		}

		return $attributes;
	}

	/**
	 * Validates the group and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @return bool
	 * @throws Cartalyst\Sentry\Groups\NameRequiredException
	 * @throws Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function validate()
	{
		// Check if name field was passed
		if ( ! $name = $this->name)
		{
			throw new NameRequiredException("A name is required for a group, none given.");
		}

		// Check if group already exists
		$query = $this->newQuery();
		$persistedGroup = $query->where('name', '=', $name)->first();

		if ($persistedGroup and $persistedGroup->getId() != $this->getId())
		{
			throw new GroupExistsException("A group already exists with name [$name], names must be unique for groups.");
		}

		return true;
	}

}
