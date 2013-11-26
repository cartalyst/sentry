<?php namespace Cartalyst\Sentry\Checkpoints;
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

use Cartalyst\Sentry\Users\UserInterface;
use RuntimeException;
use SpiExpressSecondFactor;

class SwiftIdentityException extends RuntimeException {

	/**
	 * The user which caused the exception.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $user;

	/**
	 * The user which caused the exception.
	 *
	 * @var \Cartalyst\Sentry\Users\UserInterface
	 */
	protected $response;

	/**
	 * Get the user.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set the user associated with Sentry (does not log in).
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface
	 * @return void
	 */
	public function setUser(UserInterface $user)
	{
		$this->user = $user;
	}

	/**
	 * Get the response.
	 *
	 * @return \SpiExpressSecondFactor
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Set the response.
	 *
	 * @param  \SpiExpressSecondFactor  $response
	 * @return void
	 */
	public function setResponse(SpiExpressSecondFactor $response)
	{
		$this->response = $response;
	}

}
