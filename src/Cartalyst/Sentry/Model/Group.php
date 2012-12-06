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


class Group extends EloquentModel implements GroupInterface
{
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
	 * @param   string  $permissions json
	 * @return  array
	 */
	public function getPermissions($permissions)
	{
		return ( ! is_null($permissions)) ? json_decode($permissions, true) : array();
	}

	/**
	 * Set user specific permissions
	 *
	 * @param   array  $permissions
	 * @return  string json
	 */
	public function setPermissions($permissions)
	{
		// merge permissions
		$permissions = (array) $permissions + $this->permissions;

		// loop through and adjsut permissions as needed
		foreach ($permissions as $permission => $val)
		{
			// lets make sure their is a valid permission value
			if ( ! in_array($val, $this->allowedPermissionsValues, true))
			{
				throw new \Exception($permission.' invalid permission value of '.$val. '. Must be: '.implode(', ', $this->allowedPermissionsValues));
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
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findById($id)
	{
		$user = $this->where($this->key, '=', $id)->first();

		return ($user) ?: false;
	}

	/**
	 * Find group by name
	 *
	 * @param   string  $name
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findByName($login)
	{
		$user = $this->where('name', '=', $login)->first();

		return ($user) ?: false;
	}
}