<?php namespace Cartalyst\Sentry\Cookies;
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

use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;

class IlluminateCookie implements CookieInterface {

	/**
	 * The key used in the Cookie.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * The cookie object.
	 *
	 * @var Illuminate\CookieJar
	 */
	protected $jar;

	/**
	 * The cookies queued by the guards.
	 *
	 * @var array
	 */
	protected $queuedCookies = array();

	/**
	 * Creates a new cookie instance.
	 *
	 * @param  Illuminate\CookieJar  $jar
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(CookieJar $jar, $key = null)
	{
		$this->jar = $jar;

		if (isset($key))
		{
			$this->key = $key;
		}
	}

	/**
	 * Returns the cookie key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Put a key / value pair in the cookie with an
	 * expiry.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$this->queuedCookies[] = $this->jar->make($key, $value, $minutes);
	}

	/**
	 * Put a key / value pair in the cookie forever.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->queuedCookies[] = $this->jar->forever($key, $value);
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
		return $this->jar->get($key, $default);
	}

	/**
	 * Remove an item from the cookie.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->queuedCookies[] = $this->jar->forget($key);
	}

	/**
	 * Remove all of the items from the cookie.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->forget($this->key);
	}

	/**
	 * Get the cookies queued by the driver.
	 *
	 * @return array
	 */
	public function getQueuedCookies()
	{
		return $this->queuedCookies;
	}

}