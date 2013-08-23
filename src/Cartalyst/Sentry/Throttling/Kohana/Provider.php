<?php namespace Cartalyst\Sentry\Throttling\Kohana;
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

use Cartalyst\Sentry\Throttling\ThrottleInterface;
use Cartalyst\Sentry\Throttling\ProviderInterface;
use Cartalyst\Sentry\Users\ProviderInterface as UserProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * The ORM throttle model.
	 *
	 * @var string
	 */
	protected $model = 'Throttle';

	/**
	 * The user provider used for finding users
	 * to attach throttles to.
	 *
	 * @var Cartalyst\Sentry\Users\UserInterface
	 */
	protected $userProvider;

	/**
	 * Throttling status.
	 *
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * Creates a new throttle provider.
	 *
	 * @param  Cartalyst\Sentry\Users\UserInterface  $userProvider
	 * @return void
	 */
	public function __construct(UserProviderInterface $userProvider, $model = null)
	{
		$this->userProvider = $userProvider;

		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Finds a throttler by the given user ID.
	 *
	 * @param  mixed   $id
	 * @param  string  $ipAddress
	 * @return Cartalyst\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserId($id, $ipAddress = null)
	{
		$user  = $this->userProvider->findById($id);
		$user_id = $user->id;

		$model = $this->createModel();
		$query = $model->where('user_id', '=', $user_id);

		if ($ipAddress)
		{
			$query->where('ip_address', '=', $ipAddress);
		}

		$throttle = $query->find();

		if ( ! $throttle->loaded() )
		{

			$throttle = $this->createModel();
			$values = array(
				'user_id' => $user->id
			);
			if ($ipAddress) $values['ip_address'] = $ipAddress;
			$throttle->values($values);
			$throttle->save();
		}

		return $throttle;
	}

	/**
	 * Finds a throttling interface by the given user login.
	 *
	 * @param  string  $login
	 * @param  string  $ipAddress
	 * @return Cartalyst\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserLogin($login, $ipAddress = null)
	{
		$user  = $this->userProvider->findByLogin($login);
		$user_id = $user->id;

		$model = $this->createModel();
		$query = $model->where('user_id', '=', $user_id);

		if ($ipAddress)
		{
			$query->where('ip_address', '=', $ipAddress);
		}

		$throttle = $query->find();

		if ( ! $throttle->loaded() )
		{
			$throttle = $this->createModel();
			$throttle->user_id = $user_id;
			if ($ipAddress) $throttle->ip_address = $ipAddress;
			$throttle->save();
		}

		return $throttle;
	}

	/**
	 * Enable throttling.
	 *
	 * @return void
	 */
	public function enable()
	{
		$this->enabled = true;
	}

	/**
	 * Disable throttling.
	 *
	 * @return void
	 */
	public function disable()
	{
		$this->enabled = false;
	}

	/**
	 * Check if throttling is enabled.
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return Kohana_ORM
	 */
	public function createModel()
	{
		return \ORM::factory($this->model);
	}

	/**
	 * Sets a new model class name to be used at
	 * runtime.
	 *
	 * @param  string  $model
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

}
