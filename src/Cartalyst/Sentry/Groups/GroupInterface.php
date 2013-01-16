<?php namespace Cartalyst\Sentry\Groups;
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

interface GroupInterface {

	/**
	 * Returns the group's ID.
	 *
	 * @return mixed
	 */
	public function getGroupId();

	/**
	 * Returns the group's name.
	 *
	 * @return string
	 */
	public function getGroupName();

	/**
	 * Returns permissions for the group.
	 *
	 * @return array
	 */
	public function getGroupPermissions();

	/**
	 * Saves the group.
	 *
	 * @return bool
	 */
	public function save();

	/**
	 * Delete the group.
	 *
	 * @return bool
	 */
	public function delete();

}