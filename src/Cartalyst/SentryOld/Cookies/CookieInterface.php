<?php namespace Cartalyst\Sentry;
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

interface CookieInterface {

	/**
	 * Returns the cookie key.
	 *
	 * @return string
	 */
	public function getKey();

	/**
	 * Put a key / value pair in the cookie with an
	 * expiry.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes);

	/**
	 * Put a key / value pair in the cookie forever.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value);

	/**
	 * Get the requested item from the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Remove an item from the cookie.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key);

	/**
	 * Remove all of the items from the cookie.
	 *
	 * @return void
	 */
	public function flush();

}