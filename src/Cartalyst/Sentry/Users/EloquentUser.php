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

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Permissions\PermissibleInterface;
use Cartalyst\Sentry\Persistence\PersistableInterface;

class EloquentUser extends Model implements PermissibleInterface, PersistableInterface, UserInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'users';

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
	protected $loginNames = array('email', 'username');

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
		$this->attributes['codes'] = ($codes) ? json_encode($codes) : '';
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
		array_add($codes, $code);
		$this->persistence_codes = $codes;
	}

	/**
	 * {@inheritDoc}
	 */
	public function removePersistenceCode($code)
	{
		$codes = $this->persistence_codes;
		array_forget($codes, $code);
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
