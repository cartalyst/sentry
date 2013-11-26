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

use Cartalyst\Sentry\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class ActivationCheckpoint extends BaseCheckpoint implements CheckpointInterface {

	/**
	 * Activations repository.
	 *
	 * @var \Cartalyst\Sentry\Activations\ActivationRepositoryInterface
	 */
	protected $activations;

	/**
	 * Create a new activation checkpoint.
	 *
	 * @param  \Cartalyst\Sentry\Activations\ActivationRepositoryInterface  $activations
	 * @return void
	 */
	public function __construct(ActivationRepositoryInterface $activations)
	{
		$this->activations = $activations;
	}

	/**
	 * {@inheritDoc}
	 */
	public function login(UserInterface $user)
	{
		return $this->checkActivation($user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function check(UserInterface $user)
	{
		return $this->checkActivation($user);
	}

	/**
	 * Checks the activation status of the given user.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @return bool
	 * @throws \Cartalyst\Sentry\Checkpoints\NotActivatedException
	 */
	public function checkActivation(UserInterface $user)
	{
		$exists = $this->activations->exists($user);

		if ($exists === false)
		{
			$exception = new NotActivatedException('Your account has not been activated yet.');
			$exception->setUser($user);
			throw $exception;
		}
	}

}
