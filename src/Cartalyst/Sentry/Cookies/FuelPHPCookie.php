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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cookie;

class FuelPHPCookie implements CookieInterface {

	/**
	 * Cookie key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Create a new FuelPHP cookie driver.
	 *
	 * @param  string  $key
	 */
	public function __construct($key = null)
	{
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
		Cookie::set($this->key, serialize($value), 2628000);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		$value = Cookie::get($this->key);

		if ($value)
		{
			return unserialize($value);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		Cookie::delete($this->key);
	}

}
