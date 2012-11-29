<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\UserGroupInterface;
use Cartalyst\Sentry\GroupInterface;


class User extends EloquentModel implements UserInterface, UserGroupInterface
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * The login column
	 *
	 * @var string
	 */
	protected $loginColumn = 'email';

	/**
	 * Allowed Permissions Values
	 */
	protected $allowedPermissionsValues = array(
		-1, // override denail
		0, // remove
		1, // allowed
	);

	/**
	 * -----------------------------------------
	 * UserInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Get user login column
	 *
	 * @return  string
	 */
	public function getLoginColumn()
	{
		return $this->loginColumn;
	}

	/**
	 * Get user login column
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findById($id)
	{
		return $this->find($id);
	}

	/**
	 * Get user by login value
	 *
	 * @param   string  $login
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login)
	{
		$user = $this->where($this->loginColumn, '=', $login)->first();

		return ($user) ?: false;
	}

	/**
	 * Get user by credentials
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials($login, $password)
	{
		$user = $this->findByLogin($login);

		if ($user and $password === $user->password)
		{
			return $user;
		}

		return false;
	}

	public function getPermissions($permissions)
	{
		return ( ! is_null($permissions)) ? json_decode($permissions, true) : array();
	}

	public function setPermissions($permissions)
	{
		// merge permissions
		$permissions = $permissions + $this->permissions;

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

	/**
	 * -----------------------------------------
	 * UserGroupInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Get user's groups
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function getGroups()
	{
		return $this->groups()->where('user_id', '=', 1)->get();
	}

	/**
	 * Add user to group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function addGroup($group)
	{
		if ( $group instanceof GroupInterface or is_int($group))
		{
			return $this->groups()->attach($group);
		}

		return false;
	}

	/**
	 * Add user to multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function addGroups(array $groups)
	{
		foreach ($groups as $group)
		{
			$this->addGroup($group);
		}
	}

	/**
	 * Remove user from group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function removeGroup($group)
	{
		if ( $group instanceof GroupInterface or is_int($group))
		{
			return $this->groups()->detach($group);
		}

		return false;
	}

	/**
	 * Remove user from multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function removeGroups(array $groups)
	{
		foreach ($groups as $group)
		{
			$this->removeGroup($group);
		}
	}

	/**
	 * See if user is in a group
	 *
	 * @param   integer  $group
	 * @return  bool
	 */
	public function inGroup($group)
	{
		foreach ($this->getGroups() as $_group)
		{
			if ($group == $_group->id)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * relate users to groups
	 */
	protected function groups()
	{
		return $this->belongsToMany(__NAMESPACE__.'\\Group', 'user_group', 'user_id', 'group_id');
	}

}