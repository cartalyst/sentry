<?php namespace Cartalyst\Sentry\Session;
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

use Cartalyst\Sentry\SessionInterface;
use Illuminate\Session\Store as SessionStore;
use Session;

class Laravel implements SessionInterface
{
	protected $key = 'sentry';

	protected $session;

	public function __construct(SessionStore $sessionDriver)
	{
		$this->session = $sessionDriver;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function put($key, $value)
	{
		return $this->session->put($key, $value);
	}

	public function get($key, $default = null)
	{
		return $this->session->get($key, $default);
	}

	public function forget($key)
	{
		return $this->session->forget($key);
	}

	public function flush()
	{
		return $this->forget($this->key);
	}
}