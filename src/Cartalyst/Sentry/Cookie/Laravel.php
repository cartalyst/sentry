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

	protected $key = 'sentryRemember';

	protected $cookie;

	/**
	 * Create a new cookie manager instance.
	 *
	 * @param  Illuminate\CookieJar  $cookieProvider
	 * @return void
	 */
	public function __construct(CookieJar $cookieDriver)
	{
		$this->cookie = $cookieDriver;
	}

	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Create a new cookie instance.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @return Symfony\Component\HttpFoundation\Cookie
	 */
	public function put($key, $value, $minutes)
	{
		return $this->setCookie($this->cookie->make($key, $value, $minutes));
	}

	/**
	 * Create a cookie that lasts "forever" (five years).
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @return Symfony\Component\HttpFoundation\Cookie
	 */
	public function forever($key, $value)
	{
		return $this->setCookie($this->cookie->forever($key, $value));
	}

	/**
	 * Get the value of the given cookie.
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
	 * Expire the given cookie.
	 *
	 * @param  string  $name
	 * @return Symfony\Component\HttpFoundation\Cookie
	 */
	public function forget($key)
	{
		return $this->setCookie($this->cookie->forget($key));
	}

	public function flush()
	{
		return $this->forget($this->key);
	}

	protected function setCookie($cookie)
	{
		// We manually set the cookie since L4 requires you to attach
		// it it a response which we don't have
		return setcookie(
			$cookie->getName(),
			$cookie->getValue(),
			$cookie->getExpiresTime(),
			$cookie->getPath(),
			$cookie->getDomain(),
			$cookie->isSecure(),
			$cookie->isHttpOnly()
		);
	}

}