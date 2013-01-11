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

class NativeCookieException extends \Exception {};

class NativeCookie implements CookieInterface {

	/**
	 * The key used in the Cookie.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * The value of the actual Cookie.
	 *
	 * @var string
	 */
	protected $value = null;

	/**
	 * The lifetime of the actual Cookie.
	 *
	 * @var int
	 */
	protected $lifetime = null;

	/**
	 * Default settings
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Creates a new cookie instance.
	 *
	 * @param  Illuminate\Cookie\CookieJar  $jar
	 * @return void
	 */
	public function __construct($config = array())
	{
		// Defining default settings
		$sentryDefaults = array(
			'name'      => $this->key,
			'time'      => time() + 300,
			'domain'    => '',
			'path'      => '/',
			'secure'    => false,
			'httpOnly'  => false,
		);

		// Merging settings
		$this->defaults = array_merge($sentryDefaults, $config);
	}

	/**
	 * Returns the cookie key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		if ( ! is_null($this->key))
		{
			return $this->key;
		}
		else
		{
			throw new NativeCookieException("Can't get key of current cookie since it hasn't been set yet!");
		}
	}

	/**
	 * Create a cookie.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		$lifetime = time() + $minutes;

		setcookie(
			$key,
			$value,
			$lifetime,
			$this->defaults['path'],
			$this->defaults['domain'],
			$this->defaults['secure'],
			$this->defaults['httpOnly']
		);

		$this->value    = $value;
		$this->lifetime = $lifetime;
	}

	/**
	 * Create a cookie which lasts "forever".
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->put($key, $value, time() + 60*60*24*31*12*5);
	}

	/**
	 * Get the requested cookie's value.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (isset($_COOKIE[$key]))
		{
			return $_COOKIE[$key];
		}
		elseif ( ! is_null($default))
		{
			return $default;
		}
		else
		{
			throw new NativeCookieException("Requested cookie doesn't exist!");
		}
	}

	/**
	 * Remove the cookie.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->put($key, null, time() - 65535);
	}

	/**
	 * Alias for forget().
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->forget($this->key);
	}

	/**
	 * Get the cookies queued by the driver (We don't queue them natively).
	 *
	 * @return array
	 */
	public function getQueuedCookies()
	{
		return array();
	}

	/**
	 * Returns the value of the last set cookie.
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Returns the lifetime of the last set cookie.
	 *
	 * @return mixed
	 */
	public function getLifeTime()
	{
		return $this->lifetime;
	}

}