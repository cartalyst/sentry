<?php namespace Cartalyst\Sentry\Model;
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

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\GroupInterface;
use Cartalyst\Sentry\GroupExistsException;
use Cartalyst\Sentry\GroupNotFoundException;
use Cartalyst\Sentry\InvalidPermissionException;

class Group extends EloquentModel implements GroupInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'groups';

	/**
	 * Allowed Permissions Values
	 * options:
	 *    0 => delete
	 *    1 => add
	 */
	protected $allowedPermissionsValues = array(0, 1);

	/**
	 * Get user specific permissions
	 *
	 * @param  string  $permissions json
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
	 * @return string json
	 */
	public function setPermissions($permissions)
	{
		// merge permissions
		$permissions = (array) $permissions + (array) $this->permissions;

		// loop through and adjsut permissions as needed
		foreach ($permissions as $permission => $val)
		{
			// lets make sure their is a valid permission value
			if ( ! in_array($val, $this->allowedPermissionsValues, true))
			{
				throw new InvalidPermissionException;
			}

			// if the value is 0, delete it
			if ($val === 0)
			{
				unset($permissions[$permission]);
			}
		}

		return json_encode($permissions);
	}

	/**
	 * -----------------------------------------
	 * GroupInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Find group by id
	 *
	 * @param  integer  $id
	 * @return Cartalyst\Sentry\GroupInterface or false
	 */
	public function findById($id)
	{
		$group = $this->where($this->key, '=', $id)->first();

		if ( ! $group)
		{
			throw new GroupNotFoundException;
		}

		return ($group) ?: false;
	}

	/**
	 * Find group by name
	 *
	 * @param  string  $name
	 * @return Cartalyst\Sentry\GroupInterface or false
	 */
	public function findByName($name)
	{
		$group = $this->where('name', '=', $name)->first();

		if ( ! $group)
		{
			throw new GroupNotFoundException;
		}

		return ($group) ?: false;
	}

	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		$keyName = $this->getKeyName();

		// First we need to create a fresh query instance and touch the creation and
		// update timestamp on the model which are maintained by us for developer
		// convenience. Then we will just continue saving the model instances.
		$query = $this->newQuery();

		if ($this->timestamps)
		{
			$this->updateTimestamps();
		}

		// do some validation
		$this->validate();

		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			$query->where($keyName, '=', $this->getKey());

			$query->update($this->attributes);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			if ($this->incrementing)
			{
				$this->$keyName = $query->insertGetId($this->attributes);
			}
			else
			{
				$query->insert($this->attributes);
			}
		}

		return $this->exists = true;
	}

	protected function validate()
	{
		// check if email already exists (unique)
		// check if user already exists
		try
		{
			$group = $this->findByName($this->name);
		}
		catch (GroupNotFoundException $e)
		{
			$group = null;
		}

		if ($group and $group->id != $this->id)
		{
			throw new GroupExistsException;
		}
	}

}