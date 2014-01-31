<?php namespace Cartalyst\Sentry\Persistence;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

interface PersistenceInterface {

	/**
	 * Checks for a persistence code in the current session.
	 *
	 * @return string
	 */
	public function check();

	/**
	 * Adds a new user persistence to the current session and attaches the user.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistableInterface  $persistable
	 * @return void
	 */
	public function add(PersistableInterface $persistable);

	/**
	 * Adds a new user persistence, to remember.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistableInterface  $persistable
	 * @return void
	 */
	public function addAndRemember(PersistableInterface $persistable);

	/**
	 * Removes the persistence bound to the current session.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistableInterface  $persistable
	 * @return void
	 */
	public function remove(PersistableInterface $persistable);

	/**
	 * Flushes all persistence for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Persistence\PersistableInterface  $persistable
	 * @return void
	 */
	public function flush(PersistableInterface $persistable);

}
