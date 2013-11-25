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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Activations\ActivatableInterface;
use Cartalyst\Sentry\Permissions\PermissibleInterface;
use Cartalyst\Sentry\Persistence\PersistableInterface;
use Cartalyst\Sentry\Throttling\ThrottledInterface;
use Illuminate\Database\Eloquent\Model;

class EloquentUser extends Model implements ActivatableInterface, PermissibleInterface, PersistableInterface, ThrottledInterface, UserInterface {

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
	);

	/**
	 * {@inheritDoc}
	 */
	protected $with = array(
		// 'activations',
		// 'throttles',
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
	 * Returns an array of login column names.
	 *
	 * @return array
	 */
	public function getLoginNames()
	{
		return $this->loginNames;
	}

	/**
	 * Activation relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function activations()
	{
		return $this->hasMany('Cartalyst\Sentry\Activations\EloquentActivation', 'user_id');
	}

	/**
	 * Throttles relationship.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function throttles()
	{
		return $this->hasMany('Cartalyst\Sentry\Throttling\EloquentThrottle', 'user_id');
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
	 * Set mutator for persitence codes.
	 *
	 * @param  mixed  $codes
	 * @return void
	 */
	public function setPersistenceCodesAttribute(array $codes)
	{
		$this->attributes['persistence_codes'] = ($codes) ? json_encode($codes) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getActivations()
	{
		return $this->activations;
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
		return $this->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getThrottles()
	{
		return $this->throttles;
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

}
