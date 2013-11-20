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

class EloquentUser extends Model implements PermissibleInterface {

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

	protected $loginNames = array('email', 'username');

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

	public function getLoginNames()
	{
		return $this->loginNames;
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
