<?php namespace Cartalyst\Sentry\Swift;
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

use Cartalyst\Sentry\Users\UserInterface;
use Closure;

interface SwiftInterface {

	/**
	 * Return the Swift Identity authentication response object and code.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return array
	 */
	public function response(UserInterface $user);

	/**
	 * Set the SMS number for the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  string  $number
	 * @return bool
	 */
	public function saveNumber(UserInterface $user, $number);

	public function checkAnswer(UserInterface $user, $answer, Closure $callback);

}
