<?php namespace Cartalyst\Sentry\Users\Eloquent;
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

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserInterface;

class User extends Model implements UserInterface {

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
		'reset_password_hash',
		'activation_hash',
	);

	/**
	 * Attributes that should be hashed.
	 *
	 * @var array
	 */
	protected $hashableAttributes = array(
		'password',
		'reset_password_hash',
		'activation_hash',
		'persist_hash',
	);

	/**
	 * The login attribute.
	 *
	 * @var string
	 */
	protected $loginAttribute = 'email';

	/**
	 * Allowed permissions values.
	 *
	 * Possible options:
	 *   -1 => deny
	 *    0 => delete
	 *    1 => add
	 *
	 * @var array
	 */
	protected $allowedPermissionsValues = array(-1, 0, 1);

	/**
	 * The hasher the model uses.
	 *
	 * @var Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * Returns the user's ID.
	 *
	 * @return  mixed
	 */
	public function getId($id = false)
	{
		if ($id === false and isset($this->attributes[$keyName = $this->getKeyName()]))
		{
			$id = $this->attributes[$keyName];
		}

		return $id;
	}

	/**
	 * Returns the name for the user's login.
	 *
	 * @return string
	 */
	public function getLoginName()
	{
		return $this->loginAttribute;
	}

	/**
	 * Returns the user's login.
	 *
	 * @return mixed
	 */
	public function getLogin($login = false)
	{
		if ($login === false and isset($this->attributes[$loginName = $this->getLoginName()]))
		{
			$login = $this->attributes[$loginName];
		}

		return $login;
	}

	/**
	 * Returns the user's password (hashed).
	 *
	 * @return string
	 */
	public function getPassword($password = false)
	{
		if ($password === false and isset($this->attributes['password']))
		{
			$password = $this->attributes['password'];
		}

		return $password;
	}

	/**
	 * Returns permissions for the user.
	 *
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Check if the user is activated.
	 *
	 * @return bool
	 */
	public function isActivated()
	{
		return (bool) $this->activated;
	}

	/**
	 * Get mutator for giving the activated property.
	 *
	 * @param  mixed  $activated
	 * @return bool
	 */
	public function giveActivated($activated)
	{
		return (bool) $activated;
	}

	/**
	 * Mutator for giving permissions.
	 *
	 * @param  mixed  $permissions
	 * @return array  $_permissions
	 */
	public function givePermissions($permissions)
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
	 * @return string
	 */
	public function takePermissions(array $permissions)
	{
		// Merge permissions
		$permissions = array_merge($this->getPermissions(), $permissions);

		// Loop through and adjust permissions as needed
		foreach ($permissions as $permission => $value)
		{
			// Lets make sure there is a valid permission value
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

		$this->attributes['permissions'] = json_encode($permissions);
	}

	/**
	 * Checks if the user is a super user - has
	 * access to everything regardless of permissions.
	 *
	 * @return bool
	 */
	public function isSuperUser()
	{
		$permissions = $this->getPermissions();

		if ( ! array_key_exists('superuser', $permissions))
		{
			return false;
		}

		return $permissions['superuser'] == 1;
	}

	/**
	 * Validates the user and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @return bool
	 * @throws Cartalyst\Sentry\Users\LoginRequiredException
	 * @throws Cartalyst\Sentry\Users\PasswordRequiredException
	 * @throws Cartalyst\Sentry\Users\UserExistsException
	 */
	public function validate()
	{
		if ( ! $login = $this->{$this->loginAttribute})
		{
			throw new LoginRequiredException("A login is required for a user, none given.");
		}

		if ( ! $password = $this->password)
		{
			throw new PasswordRequiredException("A password is required for user [$login], none given.");
		}

		// Check if the user already exists
		$query = $this->newQuery();
		$persistedUser = $query->where($this->getLoginName(), '=', $login)->first();

		if ($persistedUser and $persistedUser->getId() != $this->getId())
		{
			throw new UserExistsException("A user already exists with login [$login], logins must be unique for users.");
		}

		return true;
	}

	/**
	 * Saves the user.
	 *
	 * @return bool
	 */
	public function save()
	{
		$this->validate();
		return parent::save();
	}

	/**
	 * Delete the user.
	 *
	 * @return bool
	 */
	public function delete()
	{
		$this->groups()->detach();
		return parent::delete();
	}

	/**
	 * Gets a code for when the user is
	 * persisted to a cookie or session which
	 * identifies the user.
	 *
	 * @return string
	 */
	public function getPersistCode()
	{
		$this->persist_hash = $this->getRandomString();

		// Our code got hashed
		$persistCode = $this->persist_hash;

		$this->save();

		return $persistCode;
	}

	/**
	 * Checks the given persist code.
	 *
	 * @param  string  $persistCode
	 * @return bool
	 */
	public function checkPersistCode($persistCode)
	{
		if ( ! $persistCode)
		{
			return false;
		}

		return $persistCode == $this->persist_hash;
	}

	/**
	 * Get an activation code for the given user.
	 *
	 * @return string
	 */
	public function getActivationCode()
	{
		$this->activation_hash = $this->getRandomString();

		// Our code got hashed
		$activationCode = $this->activation_hash;

		$this->save();

		return $activationCode;
	}

	/**
	 * Attempts to activate the given user by checking
	 * the activate code.
	 *
	 * @param  string  $activationCode
	 * @return bool
	 */
	public function attemptActivation($activationCode)
	{
		if ($this->activated)
		{
			return true;
		}

		if ($activationCode == $this->activation_hash)
		{
			$this->activation_hash = null;
			$this->activated = true;
			return $this->save();
		}

		return false;
	}

	/**
	 * Get a reset password code for the given user.
	 *
	 * @return string
	 */
	public function getResetPasswordCode()
	{
		$this->reset_password_hash = $this->getRandomString();

		// Our code got hashed
		$resetCode = $this->reset_password_hash;

		$this->save();

		return $resetCode;
	}

	/**
	 * Checks if the provided user reset password code is
	 * valid without actually resetting the password.
	 *
	 * @param  string  $resetCode
	 * @return bool
	 */
	public function checkResetPasswordCode($resetCode)
	{
		return ($this->reset_password_hash == $resetCode);
	}

	/**
	 * Attemps to reset a user's password by matching
	 * the reset code generated with the user's.
	 *
	 * @param  string  $resetCode
	 * @param  string  $newPassword
	 * @return bool
	 */
	public function attemptResetPassword($resetCode, $newPassword)
	{
		if ($this->checkResetPasswordCode($resetCode))
		{
			$this->password = $newPassword;
			$this->reset_password_hash = null;
			return $this->save();
		}

		return false;
	}

	/**
	 * Wipes out the data associated with resetting
	 * a password.
	 *
	 * @return void
	 */
	public function clearResetPassword()
	{
		if ($this->reset_password_hash)
		{
			$this->reset_password_hash = null;
			$this->save();
		}
	}

	/**
	 * Returns an array of groups which the given
	 * user belongs to.
	 *
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups()->get();
	}

	/**
	 * Adds the user to the given group.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function addGroup(GroupInterface $group)
	{
		if ( ! $this->inGroup($group))
		{
			$this->groups()->attach($group);
		}

		return true;
	}

	/**
	 * Removes the user from the given group.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function removeGroup(GroupInterface $group)
	{
		if ($this->inGroup($group))
		{
			$this->groups()->detach($group);
		}

		return true;
	}

	/**
	 * See if the user is in the given group.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 */
	public function inGroup(GroupInterface $group)
	{
		foreach ($this->getGroups() as $_group)
		{
			if ($_group->getId() == $group->getId())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns an array of merged permissions for each
	 * group the user is in.
	 *
	 * @return array
	 */
	public function getMergedPermissions()
	{
		$permissions = array();

		foreach ($this->getGroups() as $group)
		{
			$permissions = array_merge($permissions, $group->getPermissions());
		}

		$permissions = array_merge($permissions, $this->getPermissions());

		return $permissions;
	}

	/**
	 * See if a user has a required permission. Permissions
	 * are merged from all groups the user belongs to
	 * and then are checked against the passed permission.
	 *
	 * @param  string  $permission
	 * @return bool
	 */
	public function hasAccess($permission)
	{
		if ($this->isSuperUser())
		{
			return true;
		}

		$permissions = $this->getMergedPermissions();

		return (array_key_exists($permission, $permissions) and $permissions[$permission] == 1);
	}

	/**
	 * Returns the relationship between users and groups.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany('Cartalyst\Sentry\Groups\Eloquent\Group', 'users_groups');
	}

	/**
	 * Sets the hasher for the user.
	 *
	 * @param  Cartalyst\Sentry\Hashing\HasherInterface  $hasher
	 * @return void
	 */
	public function setHasher(HasherInterface $hasher)
	{
		$this->hasher = $hasher;
	}

	/**
	 * Returns the hasher for the user.
	 *
	 * @return Cartalyst\Sentry\Hashing\HasherInterface
	 */
	public function getHasher()
	{
		return $this->hasher;
	}

	/**
	 * Check string against hashed string.
	 *
	 * @param  string  $string
	 * @param  string  $hashedString
	 * @return bool
	 * @throws RuntimeException
	 */
	public function checkHash($string, $hashedString)
	{
		if ( ! $this->hasher)
		{
			throw new \RuntimeException("A hasher has not been provided for the user.");
		}

		return $this->hasher->checkHash($string, $hashedString);
	}

	/**
	 * Hash string.
	 *
	 * @param  string  $string
	 * @return string
	 * @throws RuntimeException
	 */
	public function hash($string)
	{
		if ( ! $this->hasher)
		{
			throw new \RuntimeException("A hasher has not been provided for the user.");
		}

		return $this->hasher->hash($string);
	}

	/**
	 * Generate a random string. If your server has
	 * @return string
	 */
	public function getRandomString($length = 42)
	{
		// We'll check if the user has OpenSSL installed with PHP. If they do
		// we'll use a better method of getting a random string. Otherwise, we'll
		// fallback to a reasonably reliable method.
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			// We generate twice as many bytes here because we want to ensure we have
			// enough after we base64 encode it to get the length we need because we
			// take out the "/", "+", and "=" characters.
			$bytes = openssl_random_pseudo_bytes($length * 2, $strong);

			// We want to stop execution if the key fails because, well, that is bad.
			if ($bytes === false)
			{
				throw new \RuntimeException('Error generating random string.');
			}

			return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
		}

		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}

	/**
	 * Returns an array of hashable attributes.
	 *
	 * @return array
	 */
	public function getHashableAttributes()
	{
		return $this->hashableAttributes;
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
		// Hash required fields when necessary
		if (in_array($key, $this->hashableAttributes) and ! empty($value))
		{
			$value = $this->hash($value);
		}

		return parent::setAttribute($key, $value);
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$result = parent::toArray();

		if (isset($result['activated']))
		{
			$result['activated'] = $this->getActivated($result['activated']);
		}
		if (isset($result['permissions']))
		{
			$result['permissions'] = $this->getPermissions($result['permissions']);
		}
		if (isset($result['suspended_at']))
		{
			$result['suspended_at'] = $result['suspended_at']->format('Y-m-d H:i:s');
		}

		return $result;
	}

}
