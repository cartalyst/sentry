<?php namespace Cartalyst\Sentry\Cookie;
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

use Cartalyst\Sentry\CookieInterface;
use Illuminate\CookieJar;
use Session;

class Laravel implements CookieInterface {

	/**
	 * The key used in the Cookie.
	 *
	 * @var string
	 */
	protected $key = 'sentry_cookie';

	/**
	 * The cookie object.
	 *
	 * @var Illuminate\CookieJar
	 */
	protected $cookie;

	/**
	 * Creates a new cookie instance.
	 *
	 * @var  Illuminate\CookieJar
	 */
	public function __construct(CookieJar $cookieDriver)
	{
		$this->cookie = $cookieDriver;
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
		return $this->setCookie($this->cookie->make($key, $value, $minutes));
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
		return $this->setCookie($this->cookie->forever($key, $value));
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
		return $this->cookie->get($key, $default);
	}

	/**
	 * Remove an item from the cookie.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		return $this->setCookie($this->cookie->forget($key));
	}

	/**
	 * Remove all of the items from the cookie.
	 *
	 * @return void
	 */
	public function flush()
	{
		return $this->forget($this->key);
	}

	/**
	 * Writes to the cookie object.
	 *
	 * @param  Illuminate\CookieJar  $cookie
	 * @return bool
	 */
	protected function setCookie($cookie)
	{
		// we manually set the cookie since l4 requires you to attach it it a response which we don't have
		return setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
	}
}