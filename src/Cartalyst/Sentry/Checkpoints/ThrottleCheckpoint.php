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

use Cartalyst\Sentry\Throttling\ThrottleRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class ThrottleCheckpoint implements CheckpointInterface {

	/**
	 * Throttle repository.
	 *
	 * @var \Cartalyst\Sentry\Throttling\ThrottleRepositoryInterface
	 */
	protected $throttle;

	/**
	 * The cached IP address, used for checkpoints checks.
	 *
	 * @var string
	 */
	protected $ipAddress;

	public function __construct(ThrottleRepositoryInterface $throttle, $ipAddress)
	{
		$this->throttle  = $throttle;
		$this->ipAddress = $ipAddress;
	}

	/**
	 * {@inheritDoc}
	 */
	public function login(UserInterface $user)
	{
		return $this->checkThrottling($user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function check(UserInterface $user)
	{
		return $this->checkThrottling($user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function fail(UserInterface $user = null)
	{
		$this->throttle->log($this->ipAddress, $user);
	}

	/**
	 * Checks the throttling status of the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 */
	public function checkThrottling(UserInterface $user)
	{
		$globalDelay = $this->throttle->globalDelay();

		if ($globalDelay > 0)
		{
			$this->throwException("Gobal throttling prohibits users from logging in for another [$globalDelay] second(s).", 'global', $globalDelay);
		}

		if (isset($this->ipAddress))
		{
			$ipDelay = $this->throttle->ipDelay($this->ipAddress);

			if ($ipDelay > 0)
			{
				$this->throwException("IP address throttling prohibits you from logging in for another [$ipDelay] second(s).", 'ip', $ipDelay);
			}
		}

		if (isset($user))
		{
			$userDelay = $this->throttle->userDelay($user);

			if ($ipDelay > 0)
			{
				$this->throwException("User throttling prohibits your account being accessed in for another [$ipDelay] second(s).", 'user', $userDelay);
			}
		}

		return true;
	}

	/**
	 * Throws a throttling exception.
	 *
	 * @param  string  $message
	 * @param  string  $type
	 * @param  int  $delay
	 * @throws \Cartalyst\Sentry\Checkpoints\ThrottlingException
	 */
	protected function throwException($message, $type, $delay)
	{
		$exception = new ThrottlingException($message);
		$exception->setDelay($delay);
		$exception->setType($type);
		throw $exception;
	}

}
