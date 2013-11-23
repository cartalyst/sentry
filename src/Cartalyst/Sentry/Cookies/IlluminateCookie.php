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
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class IlluminateCookie implements CookieInterface {

	/**
	 * The current request.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

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
	 * The cookie to be sent with the response.
	 *
	 * @var \Symfony\Component\HttpFoundation\Cookie
	 */
	protected $cookie;

	/**
	 * Create a new Illuminate cookie driver.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Cookie\CookieJar  $jar
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(Request $request, CookieJar $jar, $key = null)
	{
		$this->request = $request;
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
		$this->cookie = $this->jar->forever($this->key, $value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return $this->request->cookie($this->key);
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
