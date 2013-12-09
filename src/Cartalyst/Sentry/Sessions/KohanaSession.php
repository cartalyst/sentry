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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Kohana_Session as Session;

class KohanaSession implements SessionInterface {

	/**
	 * The Kohana session driver.
	 *
	 * @param  Kohana_Session
	 */
	protected $store;

	/**
	 * Session key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Creates a new Kohana Session driver for Sentry.
	 *
	 * @param  \Session  $store
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
		$this->store->set($this->key, serialize($value));
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return unserialize($this->store->get($this->key));
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		$this->store->delete($this->key);
	}

}
