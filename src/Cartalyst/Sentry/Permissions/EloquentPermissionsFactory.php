<?php namespace Cartalyst\Sentry\Permissions;
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

class EloquentPermissionsFactory {

	/**
	 * Associated user.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * Cached permissions object.
	 *
	 * @var \Cartalyst\Sentry\Permissions\PermissionsInterface
	 */
	protected $permissions;

	/**
	 * Create a new Eloquent permissions instance.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 */
	public function __construct(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Get the permissions instance.
	 *
	 * @return \Cartalyst\Sentry\Permissions\PermissionsInterface
	 */
	public function getPermissions()
	{
		if ($this->permissions === null)
		{
			list($userPermissions, $groupPermissions) = $this->loadPermissions();

			$permissions = $this->createPermissions();
			$permissions->setUserPermissiosn($userPermissions);
			$permissions->setGroupPermissions($groupPermissions);

			$this->permissions = $permissions;
		}

		return $this->permissions;
	}

	/**
	 * Loads permissions from the User instance.
	 *
	 * @return void
	 */
	protected function loadPermissions()
	{
		$userPermissions = $this->user->permissions;
		$groupPermissions = array();

		foreach ($this->user->groups as $group)
		{
			$groupPermissions[] = $group->permissions;
		}

		return array($userPermissions, $groupPermissions);
	}

	/**
	 * Creates a permissions object.
	 *
	 * @return \Cartalyst\Sentry\Permissions\SentryPermissions
	 */
	protected function createPermissions()
	{
		return new SentryPermissions;
	}

}
