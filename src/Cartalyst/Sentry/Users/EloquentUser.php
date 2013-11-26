<?php namespace Cartalyst\Sentry\Users;
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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Activations\ActivatableInterface;
use Cartalyst\Sentry\Groups\GroupableInterface;
use Cartalyst\Sentry\Permissions\PermissibleInterface;
use Cartalyst\Sentry\Permissions\SentryPermissions;
use Cartalyst\Sentry\Persistence\PersistableInterface;
use Cartalyst\Sentry\Throttling\ThrottledInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentUser extends Model implements GroupableInterface, PermissibleInterface, PersistableInterface, UserInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'users';

	/**
	 * {@inheritDoc}
	 */
	protected $fillable = array(
		'email',
		'password',
		'last_login',
	);

	/**
	 * Cached permissions instance for the given user.
	 *
	 * @var \Cartalyst\Sentry\Permissions\PermissionsInterface
	 */
	protected $permissionsInstance;

	/**
	 * Array of login column names.
	 *
	 * @var array
	 */
	protected $loginNames = array('email');

	/**
	 * The groups model name.
	 *
	 * @var string
	 */
	protected static $groupsModel = 'Cartalyst\Sentry\Groups\EloquentGroup';

	/**
	 * Returns an array of login column names.
	 *
	 * @return array
	 */
	public function getLoginNames()
	{
		return $this->loginNames;
	}

	/**
	 * Groups relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany(static::$groupsModel, 'groups_users', 'user_id', 'group_id');
	}

	/**
	 * Get mutator for persistence codes.
	 *
	 * @param  mixed  $codes
	 * @return array
	 */
	public function getPersistenceCodesAttribute($codes)
	{
		return ($codes) ? json_decode($codes, true) : array();
	}

	/**
	 * Set mutator for persistence codes.
	 *
	 * @param  mixed  $codes
	 * @return void
	 */
	public function setPersistenceCodesAttribute(array $codes)
	{
		$this->attributes['persistence_codes'] = ($codes) ? json_encode(array_values($codes)) : '';
	}

	/**
	 * Get mutator for permissions.
	 *
	 * @param  mixed  $permissions
	 * @return array
	 */
	public function getPermissionsAttribute($permissions)
	{
		return ($permissions) ? json_decode($permissions, true) : array();
	}

	/**
	 * Set mutator for permissions.
	 *
	 * @param  mixed  $permissions
	 * @return void
	 */
	public function setPermissionsAttribute(array $permissions)
	{
		$this->attributes['permissions'] = ($permissions) ? json_encode($permissions) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * {@inheritDoc}
	 */
	public function inGroup($group)
	{
		$group = array_first($this->groups, function($index, $instance) use ($group)
		{
			if ($group instanceof GroupInterface)
			{
				return ($instance === $group);
			}

			if ($instance->getGroupId() == $group)
			{
				return true;
			}

			if ($instance->getGroupSlug() == $group)
			{
				return true;
			}

			return false;
		});

		return ($group !== null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPermissions()
	{
		if ($this->permissionsInstance === null)
		{
			$this->permissionsInstance = $this->createPermissions();
		}

		return $this->permissionsInstance;
	}

	/**
	 * {@inheritDoc}
	 */
	public function generatePersistenceCode()
	{
		return str_random(32);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPersistenceCodes()
	{
		return $this->persistence_codes;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addPersistenceCode($code)
	{
		$codes = $this->persistence_codes;
		$codes[] = $code;
		$this->persistence_codes = $codes;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removePersistenceCode($code)
	{
		$codes = $this->persistence_codes;

		$index = array_search($code, $codes);

		if ($index !== false)
		{
			unset($codes[$index]);
		}

		$this->persistence_codes = $codes;
	}

	/**
	 * {@inheritDoc}
	 */
	public function savePersistenceCodes()
	{
		$this->last_login = Carbon::now();

		return $this->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserId()
	{
		return $this->getKey();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserLogin()
	{
		return $this->getAttribute($this->getUserLoginName());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserLoginName()
	{
		return reset($this->loginNames);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUserPassword()
	{
		return $this->password;
	}

	/**
	 * Creates a permissions object.
	 *
	 * @return \Cartalyst\Sentry\Permissions\PermissionsInterface
	 */
	protected function createPermissions()
	{
		$userPermissions  = $this->permissions;
		$groupPermissions = array();

		foreach ($this->groups as $group)
		{
			$groupPermissions[] = $group->permissions;
		}

		return new SentryPermissions($userPermissions, $groupPermissions);
	}

	/**
	 * Get the groups model.
	 *
	 * @return string
	 */
	public static function getGroupsModel()
	{
		return static::$groupsModel;
	}

	/**
	 * Set the groups model.
	 *
	 * @param  string  $groupsModel
	 * @return void
	 */
	public static function setGroupsModel($groupsModel)
	{
		static::$groupsModel = $groupsModel;
	}

	/**
	 * Dynamically pass missing methods to the group.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$methods = array('hasAccess', 'hasAnyAccess');

		if (in_array($method, $methods))
		{
			$permissions = $this->getPermissions();

			return call_user_func_array(array($permissions, $method), $parameters);
		}

		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}
