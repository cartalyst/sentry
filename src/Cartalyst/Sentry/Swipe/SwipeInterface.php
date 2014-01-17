<?php namespace Cartalyst\Sentry\Swipe;
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

use Cartalyst\Sentry\Users\UserInterface;
use Closure;

interface SwipeInterface {

	/**
	 * Return the Swipe Identity authentication response object and code.
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

	/**
	 * Checks the SMS answer for the given user. Pass an optional callback to be
	 * executed on successful verification of the answer to be executed while
	 * the object is in an answering state. If you choose to pass a callback,
	 * it's return value should be cascaded out of this method. If not, the
	 * boolean result of the SMS answer should be returned instead.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  string  $answer
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function checkAnswer(UserInterface $user, $answer, Closure $callback = null);

}
