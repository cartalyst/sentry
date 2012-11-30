<?php namespace Cartalyst\Sentry\Model;

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

		// loop through and remove all permissions with value of 0
		foreach ($permissions as $permission => $val)
		{
			if ( ! in_array($val, $this->allowedPermissionsValues, true))
			{
				throw new \Exception($permission.' invalid permission value of '.$val. '. Must be: '.implode(', ', $this->allowedPermissionsValues));
			}

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