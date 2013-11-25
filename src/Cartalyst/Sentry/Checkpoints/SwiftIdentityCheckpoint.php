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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Swift\SwiftInterface;
use Cartalyst\Sentry\Users\UserInterface;
use SpiExpressSecondFactor;

class SwiftIdentityCheckpoint extends BaseCheckpoint implements CheckpointInterface {

	protected $swift;

	public function __construct(SwiftInterface $swift)
	{
		$this->swift = $swift;
	}

	/**
	 * {@inheritDoc}
	 */
	public function login(UserInterface $user)
	{
		if ($this->swift->isAnswering())
		{
			return true;
		}

		list($response, $code) = $this->swift->response($user);

		switch ($code)
		{
			case NEED_REGISTER_SMS:
				$message = 'User needs to register SMS.';
				break;

			case NEED_REGISTER_SWIPE:
				$message = 'User needs to register their swipe application.';
				break;

			case RC_SWIPE_TIMEOUT:
				return false;

			case RC_SWIPE_ACCEPTED:
				return true;

			case RC_SWIPE_REJECTED:
				$message = 'User has rejected swipe request.';

			case RC_SMS_DELIVERED:
				$message = 'SMS was delivered to user.';
				break;

			case RC_ERROR:
				$message = 'An error occured with Swift Identity.';
				break;

			case RC_APP_DOES_NOT_EXIST:
				$message = 'Your Swift Identity app is misconfigured.';
				break;
		}

		$this->throwException($message, $code, $user, $response);
	}

	/**
	 * {@inheritDoc}
	 */
	public function check(UserInterface $user)
	{
		return true;
	}

	/**
	 * Throws an exception due to an unsuccessful Swift Identity authentication.
	 *
	 * @param  string  $message
	 * @param  int  $code
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  \SpiExpressSecondFactor  $response
	 * @throws \Cartalyst\Sentry\Checkpoints\SwiftIdentityException
	 */
	protected function throwException($message, $code, UserInterface $user, SpiExpressSecondFactor $response)
	{
		$exception = new SwiftIdentityException($message, $code);
		$exception->setUser($user);
		$exception->setResponse($response);
		throw $exception;
	}

}
