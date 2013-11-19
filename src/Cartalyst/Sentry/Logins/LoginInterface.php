<?php namespace Cartalyst\Sentry\Logins;
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

use Cartalyst\Sentry\Sessions\SessionInterface;
use Cartalyst\Sentry\Users\UserRepositoryInterface;

interface LoginInterface {

	/**
	 * Checks to see if the given user is logged into a session.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $user
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @return bool
	 */
	public function check(UserRepositoryInterface $user, SessionInterface $session);

	/**
	 * Adds a new user login.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $user
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function add(UserRepositoryInterface $user, SessionInterface $session);

	/**
	 * Adds a new user login.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $user
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function remove(UserRepositoryInterface $user, SessionInterface $session);

	/**
	 * Adds a new user login.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface  $user
	 * @param  \Cartalyst\Sentry\Sessions\SessionInterface  $session
	 * @return bool
	 * @todo   IS this where we would throw exceptions? (Not Activated etc)
	 */
	public function flush(UserRepositoryInterface $user, SessionInterface $session);

}
