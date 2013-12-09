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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class StrictPermissions extends BasePermissions implements PermissionsInterface {

	/**
	 * {@inheritDoc}
	 */
	protected function createPreparedPermissions()
	{
		$prepared = array();

		if ( ! empty($this->secondaryPermissions))
		{
			foreach ($this->secondaryPermissions as $permissions)
			{
				$this->preparePermissions($prepared, $permissions);
			}
		}

		if ( ! empty($this->permissions))
		{
			$permissions = array();
			$this->preparePermissions($permissions, $this->permissions);
			$prepared = array_merge($prepared, $permissions);
		}

		return $prepared;
	}

}
