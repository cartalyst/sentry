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

use Cartalyst\Sentry\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class ActivationCheckpoint implements CheckpointInterface {

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
	 */
	public function __construct(ActivationRepositoryInterface $activations)
	{
		$this->activations = $activations;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(UserInterface $user = null)
	{
		// We only intercept successful logins
		if ($user === null)
		{
			return;
		}

		$exists = $this->activations->exists($user);

		if ($exists === false)
		{
			$exception = new NotActivatedException('Your account has not been activated yet.');
			$exception->setUser($user);
			throw $exception;
		}
	}

}
