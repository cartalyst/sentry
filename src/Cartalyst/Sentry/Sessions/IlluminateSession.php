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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Session\Store as SessionStore;

class IlluminateSession implements SessionInterface {

	/**
	 * Session store object.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Session key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Create a new Illuminate Session driver.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @param  string  $key
	 */
	public function __construct(SessionStore $session, $key = null)
	{
		$this->session = $session;

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
		$this->session->put($this->key, $value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return $this->session->get($this->key);
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		$this->session->forget($this->key);
	}

}
