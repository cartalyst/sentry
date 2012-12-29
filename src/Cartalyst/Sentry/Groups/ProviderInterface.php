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

class GroupExistsException extends \RuntimeException {}
class GroupNotFoundException extends \RuntimeException {}
class NameFieldRequiredException extends \RuntimeException {}

interface ProviderInterface {

	/**
	 * Find group by ID.
	 *
	 * @param  int  $id
	 * @return Cartalyst\Sentry\GroupInterface  $group
	 * @throws Cartalyst\Sentry\GroupNotFoundException
	 */
	public function findById($id);

	/**
	 * Find group by name.
	 *
	 * @param  string  $name
	 * @return Cartalyst\Sentry\GroupInterface  $group
	 * @throws Cartalyst\Sentry\GroupNotFoundException
	 */
	public function findByName($name);

	/**
	 * Validates the group and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 * @throws Cartalyst\Sentry\Groups\NameFieldRequiredException
	 * @throws Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function validate(GroupInterface $group);

}