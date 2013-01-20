<?php namespace Cartalyst\Sentry\Sessions\Native;
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

class MemcacheSession implements SessionInterface {

	/**
	 * The key used in the Session.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Session store object.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Creates a new Illuminate based Session driver
	 * for Sentry.
	 *
	 * @param  Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(SessionStore $session, $key = null)
	{
		$this->session = $session;

		if (isset($key))
		{
			$this->key = $key;
		}
	}

	/**
	 * Returns the session key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Put a key / value pair in the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function put($key, $value)
	{
		$this->session->put($key, $value);
	}

	/**
	 * Get the requested item from the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		return $this->session->get($key, $default);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->session->forget($key);
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->forget($this->key);
	}

}