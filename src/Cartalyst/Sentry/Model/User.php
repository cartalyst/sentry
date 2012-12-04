<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\UserGroupInterface;
use Cartalyst\Sentry\GroupInterface;
use Cartalyst\Sentry\HashInterface;
use Cartalyst\Sentry\Hash\Bcrypt;


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
	protected $hidden = array(
		'password',
		'temp_password',
		'reset_password_hash',
		'activation_hash',
	);

	/**
	 * The login column
	 *
	 * @var string
	 */
	protected $loginColumn = 'email';

	/**
	 * Allowed Permissions Values
	 * options:
	 *   -1 => deny
	 *    0 => delete
	 *    1 => add
	 */
	protected $allowedPermissionsValues = array(-1, 0, 1);

	/**
	 * Super user permissions, gives access to everything
	 *
	 * @var string
	 */
	protected $superUser = 'superuser';

	/**
	 * Hashing Interface
	 *
	 * @var Cartalyst\Sentry\HashInterface
	 */
	protected $hashInterface;

	/**
	 * Fields that should be hashed
	 *
	 * @var array
	 */
	protected $hashedFields = array(
		'password',
		'temp_password',
		'reset_password_hash',
	);

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes = array(), HashInterface $hashInterface = null)
	{
		$this->fill($attributes);
		$this->hashInterface = ($hashInterface) ?: new Bcrypt;
	}

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
		return $this->where($this->loginColumn, '=', $login)->first();
	}

	/**
	 * Get user by credentials
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials(array $attributes)
	{
		$query = $this->newQuery();
		$hashedAttributes = array();

		// build query from given attributes
		foreach ($attributes as $attribute => $value)
		{
			// remove hashed attributes to check later as we need to check these
			// values after we retrieved them because of salts
			if (in_array($attribute, $this->hashedFields))
			{
				$hashedAttributes += array($attribute => $value);
				continue;
			}

			$query = $query->where($attribute, '=', $value);
		}

		// retrieve the user
		$user = $query->first();

		if ( ! $user)
		{
			return false;
		}

		// now we check for hashed values to make sure they match as well
		foreach ($hashedAttributes as $attribute => $value)
		{
			if ( ! $this->checkHash($value, $user->password))
			{
				return false;
			}
		}

		return ($user) ?: false;
	}

	/**
	 * Registers a user
	 *
	 * @return string
	 */
	public function register()
	{
		// generate an activation code
		$activationCode = $this->randomString();

		$this->activation_hash = $this->hash($activationCode);
		$this->activated = 0;
		$this->save();

		return $activationCode;
	}

	/**
	 * Activate a user
	 *
	 * @param   string  $login
	 * @param   string  $activationCode
	 * @return  bool
	 */
	public function activate($activationCode)
	{
		// don't save if they are already activated...
		if ($this->activated)
		{
			return true;
		}

		// if the user exists and the activation code matches, activate and update required fields
		if ($this->exists and $this->checkHash($activationCode, $this->activation_hash))
		{
			$this->activation_hash = null;
			$this->activated = 1;
			$this->save();

			return true;
		}

		return false;
	}

	/**
	 * Check if user is activated
	 *
	 * @param   UserInterface  $user
	 * @return  bool
	 */
	public function isActivated()
	{
		return $this->activated;
	}

	/**
	 * Reset a user's password
	 *
	 * @param   string   $login
	 * @param   string   $password
	 * @return  string|false
	 */
	public function resetPassword($password)
	{
		// generate a reset code
		$resetCode = $this->randomString();

		$this->temp_password = $password;
		$this->reset_password_hash = $resetCode;
		$this->save();

		return $resetCode;
	}

	/**
	 * Confirm a password reset request
	 *
	 * @param   string  $login
	 * @param   string  $resetCode
	 * @return  bool
	 */
	public function resetPasswordConfirm($resetCode)
	{
		// if the user exists and the reset code matches, reset and update required fields
		if ($this->exists and $this->checkHash($resetCode, $this->reset_password_hash))
		{
			$this->password = $this->temp_password;
			$this->temp_password = null;
			$this->reset_password_hash = null;
			$this->save();

			return true;
		}

		return false;
	}

	/**
	 * Clears Password Reset Fields
	 *
	 * @param   UserInterface  $user
	 * @return  $user
	 */
	public function clearResetPassword()
	{
		if ($this->temp_password or $this->reset_password_hash)
		{
			$this->temp_password = null;
			$this->reset_password_hash = null;
			$this->save();
		}
	}

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
	 * See if a user has a required permission
	 *
	 * @param   string  $permission
	 * @return  bool
	 */
	public function hasAccess($permission)
	{
		// check if they are a super user
		if (array_key_exists('superuser', $this->permissions) and $this->permissions['superuser'] === 1)
		{
			return true;
		}

		// merge permissions together, user permissions override group permissions
		$mergedPermissions = $this->permissions + $this->getGroupPermissions();

		// check to see if user has access with merged permissions
		if ( array_key_exists($permission, $mergedPermissions) and $mergedPermissions[$permission] === 1)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get merged group permissions
	 *
	 * @return  array
	 */
	public function getGroupPermissions()
	{
		$permissions = array();

		// loop through user groups and merge their permissions
		foreach ($this->groups as $group)
		{
			$permissions += $group->permissions;
		}

		return $permissions;
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
		return $this->groups()->where('user_id', '=', $this->id)->get();
	}

	/**
	 * Add user to group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function addGroup($group)
	{
		// check to see if they are already in the group
		if ($this->inGroup($group))
		{
			return true;
		}

		// if a group object was passed, check to see if it exists
		if ( $group instanceof GroupInterface)
		{
			if ( ! $group->exists)
			{
				return false;
			}
		}
		// otherwise query data passed to make sure the group exists
		else
		{
			$_group = new Group();

			$field = (is_int($group)) ? 'id' : 'name';

			$group = $_group->where($field, '=', $group)->first();

			if ( ! $group)
			{
				return false;
			}
		}

		return $this->groups()->attach($group);
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
		if ( ! $this->inGroup($group))
		{
			return true;
		}

		return $this->groups()->detach($group);
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
	 * @param   integer|string|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function inGroup($group)
	{
		foreach ($this->getGroups() as $userGroups)
		{
			if ($group instanceof GroupInterface and $group->id === $userGroups->group_id)
			{
				return true;
			}
			elseif (is_int($group) and $group === $userGroups->group_id)
			{
				return true;
			}
			elseif ($group === $userGroups->name)
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
		return $this->belongsToMany(__NAMESPACE__.'\\Group', 'user_group');
	}

	/**
	 * Hash String
	 *
	 * @param   string  $str
	 * @return  string
	 */
	protected function hash($str)
	{
		return $this->hashInterface->hash($str);
	}

	/**
	 * Check Hash Values
	 *
	 * @param   string  $str
	 * @param   string  $hashed_str
	 * @return  bool
	 */
	protected function checkHash($str, $hashed_str)
	{
		return $this->hashInterface->checkHash($str, $hashed_str);
	}

	/**
	 * Generate a random string
	 *
	 * @return  string
	 */
	protected function randomString()
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, 40);
	}

	/**
	 * Set a given attribute on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		// hash required fields when necessary
		if (in_array($key, $this->hashedFields))
		{
			if (($key == 'password' and $value == $this->temp_password) or empty($value))
			{
				// do nothing
			}
			else
			{
				$value = $this->hash($value);
			}

		}

		// First we will check for the presence of a mutator for the set operation
		// which simply lets the developers tweak the attribute as it is set on
		// the model, such as "json_encoding" an listing of data for storage.
		if ($this->hasSetMutator($key))
		{
			$method = 'set'.camel_case($key);

			return $this->attributes[$key] = $this->$method($value);
		}

		$this->attributes[$key] = $value;
	}
}