<?php namespace Cartalyst\Sentry\Groups;
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
use Cartalyst\Sentry\Permissions\PermissibleInterface;
use Cartalyst\Sentry\Permissions\SentryPermissions;
use Cartalyst\Sentry\Persistence\PersistableInterface;
use Cartalyst\Sentry\Throttling\ThrottledInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentGroup extends Model implements GroupInterface, PermissibleInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'groups';

	/**
	 * {@inheritDoc}
	 */
	protected $fillable = array(
		'slug',
		'name',
		'permissions',
	);

	/**
	 * The users model name.
	 *
	 * @var string
	 */
	protected static $usersModel = 'Cartalyst\Sentry\Users\EloquentUser';

	/**
	 * Users relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(static::$usersModel, 'groups_users', 'group_id', 'user_id');
	}

	/**
	 * Get mutator for the "permissions" attribute.
	 *
	 * @param  mixed  $permissions
	 * @return array
	 */
	public function getPermissionsAttribute($permissions)
	{
		return $permissions ? json_decode($permissions, true) : array();
	}

	/**
	 * Set mutator for the "permissions" attribute.
	 *
	 * @param  mixed  $permissions
	 * @return void
	 */
	public function setPermissionsAttribute(array $permissions)
	{
		$this->attributes['permissions'] = $permissions ? json_encode($permissions) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroupId()
	{
		return $this->getKey();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroupSlug()
	{
		return $this->slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsers()
	{
		return $this->users;
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
	 * Creates a permissions object.
	 *
	 * @return \Cartalyst\Sentry\Permissions\PermissionsInterface
	 */
	protected function createPermissions()
	{
		return new SentryPermissions($this->permissions);
	}

	/**
	 * Get the users model.
	 *
	 * @return string
	 */
	public static function getUsersModel()
	{
		return static::$usersModel;
	}

	/**
	 * Set the users model.
	 *
	 * @param  string  $usersModel
	 * @return void
	 */
	public static function setUsersModel($usersModel)
	{
		static::$usersModel = $usersModel;
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

		$className = get_class($this);

		throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
	}

}
