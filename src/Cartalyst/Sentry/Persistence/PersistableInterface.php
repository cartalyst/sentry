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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

interface PersistableInterface {

	/**
	 * Generates a random persist code.
	 *
	 * @return  string
	 */
	public function generatePersistenceCode();

	/**
	 * Returns an array of assigned persist codes.
	 *
	 * @return array
	 */
	public function getPersistenceCodes();

	/**
	 * Adds a new persist code.
	 *
	 * @param  string  $code
	 * @return bool
	 */
	public function addPersistenceCode($code);

	/**
	 * Removes a persist code.
	 *
	 * @param  string  $code
	 * @return bool
	 */
	public function removePersistenceCode($code);

}
