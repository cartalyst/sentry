<?php namespace Cartalyst\Sentry\Cookies;
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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Container\Container;
use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;

class IlluminateCookie implements CookieInterface {

	/**
	 * The cookie object.
	 *
	 * @var \Illuminate\Cookie\CookieJar
	 */
	protected $jar;

	/**
	 * Cookie key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * The cookie to be stored.
	 *
	 * @var \Symfony\Component\HttpFoundation\Cookie
	 */
	protected $cookie;

	/**
	 * Create a new Illuminate cookie driver.
	 *
	 * @param  \Illuminate\Cookie\CookieJar  $jar
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
	 * {@inheritDoc}
	 */
	public function put($value)
	{
		$this->cookie = $this->jar->forever($this->key, $value, $minutes);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return $this->jar->get($this->key);
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		$this->cookie = $this->jar->forget($this->key);
	}

	/**
	 * Returns the Symfony cookie object associated with the Illuminate cookie.
	 *
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function getCookie()
	{
		return $this->cookie;
	}

}
