<?php namespace Cartalyst\Sentry;
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

class Sentry {

	public function login(UserRepositoryInterface $user, $remmeber = false)
	{
		$method = ($remember === true) ? 'addAndRemember' : 'add';

		return $this->logins->$method($user);
	}

	public function logout(UserRepositoryInterface $user)
	{
		return $this->logins->remove($user);
	}

}
