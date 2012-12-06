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

class Laravel implements CookieInterface
{
	protected $key = 'sentryRemember';

	protected $cookie;

	public function __construct(CookieJar $cookieDriver)
	{
		$this->cookie = $cookieDriver;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function put($key, $value, $minutes)
	{
		return $this->setCookie($this->cookie->make($key, $value, $minutes));
	}

	public function forever($key, $value)
	{
		return $this->setCookie($this->cookie->forever($key, $value));
	}

	public function get($key, $default = null)
	{
		return $this->cookie->get($key, $default);
	}

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
		// we manually set the cookie since l4 requires you to attach it it a response which we don't have
		return setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
	}
}