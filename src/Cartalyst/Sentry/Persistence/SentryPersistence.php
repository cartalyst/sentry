<?php namespace Cartalyst\Sentry\Persistence;
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

use Cartalyst\Sentry\Cookies\CookieInterface;
use Cartalyst\Sentry\Sessions\SessionInterface;

class SentryPersistence implements PersistenceInterface {

	/**
	 * Session storage driver.
	 *
	 * @var \Cartalyst\Sentry\Sessions\SessionInterface
	 */
	protected $session;

	/**
	 * Cookie storage driver.
	 *
	 * @var \Cartalyst\Sentry\Cookies\CookieInterface
	 */
	protected $cookie;

	/**
	 * Create a new Illuminate persistence repository.
	 *
	 * @param  Cartalyst\Sentry\Cookies\CookieInterface  $session
	 * @param  Cartalyst\Sentry\Sessions\SessionInterface  $cookie
	 */
	public function __construct(SessionInterface $session, CookieInterface $cookie)
	{
		$this->session = $session;
		$this->cookie  = $cookie;
	}

	/**
	 * {@inheritDoc}
	 */
	public function check()
	{
		if ($code = $this->session->get())
		{
			return $code;
		}

		if ($code = $this->cookie->get())
		{
			return $code;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function add(PersistableInterface $persistable, $remember = false)
	{
		$code = $persistable->generatePersistenceCode();

		$this->session->put($code);

		if ($remember === true)
		{
			$this->cookie->put($code);
		}

		$persistable->addPersistenceCode($code)

		return $persistable->savePersistenceCodes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function addAndRemember(PersistableInterface $persistable)
	{
		return $this->add($persistable, true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove(PersistableInterface $persistable)
	{
		$code = $this->check();

		if ($code === null)
		{
			return true;
		}

		$this->session->forget();
		$this->cookie->forget();

		$persistable->removePersistenceCode($code)

		return $persistable->savePersistenceCodes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function flush(PersistableInterface $persistable)
	{
		$this->session->forget();
		$this->cookie->forget();

		foreach ($persistable->getPersistenceCodes() as $code)
		{
			$persistable->removePersistenceCode($code);
		}

		return $persistable->savePersistenceCodes();
	}

}
