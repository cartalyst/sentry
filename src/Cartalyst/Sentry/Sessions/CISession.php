<?php namespace Cartalyst\Sentry\Sessions;
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

use CI_Session as Session;

class CISession implements SessionInterface {

	/**
	 * The CodeIgniter session driver.
	 *
	 * @param  CI_Session
	 */
	protected $store;

	/**
	 * Session key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Create a new CodeIgniter Session driver.
	 *
	 * @param  \CI_Session  $store
	 * @param  string  $key
	 */
	public function __construct(Session $store, $key = null)
	{
		$this->store = $store;

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
		$this->store->set_userdata($this->key, serialize($value));
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		$value = $this->store->userdata($this->key);

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
		$this->store->unset_userdata($this->key);
	}

}
