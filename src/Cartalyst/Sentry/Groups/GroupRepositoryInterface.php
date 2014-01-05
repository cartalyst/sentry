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

use Closure;

interface GroupRepositoryInterface {

	/**
	 * Finds all the groups.
	 *
	 * @return \Cartalyst\Sentry\Groups\GroupInterface
	 */
	public function findAll();

	/**
	 * Finds a group by the given primary key.
	 *
	 * @param  int  $id
	 * @return \Cartalyst\Sentry\Groups\GroupInterface
	 */
	public function findById($id);

	/**
	 * Finds a group by the given slug.
	 *
	 * @param  string  $slug
	 * @return \Cartalyst\Sentry\Groups\GroupInterface
	 */
	public function findBySlug($slug);

}
