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

class NativeSession implements SessionInterface {

	/**
	 * Session key.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Creates a new native session driver for Sentry.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __construct($key = null)
	{
		if (isset($key))
		{
			$this->key = $key;
		}

		$this->startSession();
	}

	/**
	 * Called upon destruction of the native session handler.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->writeSession();
	}

	/**
	 * {@inheritDoc}
	 */
	public function put($value)
	{
		$this->setSession($value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		return $this->getSession();
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		$this->forgetSession();
	}

	/**
	 * Starts the session if it does not exist.
	 *
	 * @return void
	 */
	protected function startSession()
	{
		// Check that the session hasn't already been started
		if (session_id() == '' and ! headers_sent())
		{
			session_start();
		}
	}

	/**
	 * Writes the session.
	 *
	 * @return void
	 */
	protected function writeSession()
	{
		session_write_close();
	}

	/**
	 * Unserializes a value from the session and returns it.
	 *
	 * @return mixed.
	 */
	protected function getSession()
	{
		if (isset($_SESSION[$this->key]))
		{
			$value = $_SESSION[$this->key];

			if ($value)
			{
				return unserialize($value);
			}
		}
	}

	/**
	 * Interacts with the $_SESSION global to set a property on it.
	 * The property is serialized initially.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	protected function setSession($value)
	{
		$_SESSION[$this->key] = serialize($value);
	}

	/**
	 * Forgets the Sentry session from the global $_SESSION.
	 *
	 * @return void
	 */
	protected function forgetSession()
	{
		if (isset($_SESSION[$this->key]))
		{
			unset($_SESSION[$this->key]);
		}
	}

}
