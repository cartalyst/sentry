<?php namespace Cartalyst\Sentry\Throttling;
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

use Carbon\Carbon;
use Cartalyst\Sentry\Users\UserInterface;

/**
 * @todo Switch over to eager loading where possible, under the assumption that the Eloquent user model will include the required relationship.
 */
class IlluminateThrottleRepository implements ThrottleRepositoryInterface {

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Throttling\EloquentThrottle';

	/**
	 * The interval which failed logins are checked, to prevent brute force.
	 *
	 * @var int
	 */
	protected $globalInterval = 900;

	/**
	 * Thresholds. If an array, the key is the number of failed attempts
	 * (within the global interval) and the value is the delay in
	 * seconds before another login can occur. Integer
	 * represents a limited number of attempts
	 * before throttling locks out in the
	 * current interval.
	 *
	 * @var int|array
	 */
	protected $globalThresholds = array(
		10 => 1,
		20 => 2,
		30 => 4,
		40 => 8,
		50 => 16,
		60 => 32,
	);

	/**
	 * Cached global throttles collection within the interval.
	 *
	 * @var \Illuminate\Database\Eloquent\Collection
	 */
	protected $globalThrottles;

	/**
	 * The interval at which point one IP address' failed logins are checked.
	 *
	 * @var int
	 */
	protected $ipInterval = 900;

	/**
	 * Works identical to global thresholds, except specific to an IP address.
	 *
	 * @var int|array
	 */
	protected $ipThresholds = 5;

	/**
	 * Cached IP address throttle collections within the interval.
	 *
	 * @var array
	 */
	protected $ipThrottles = array();

	/**
	 * The interval at which point failed logins for one user are checked.
	 *
	 * @var int
	 */
	protected $userInterval = 900;

	/**
	 * Works identical to global and IP address thresholds, regarding a user.
	 *
	 * @var int|array
	 */
	protected $userThresholds = 5;

	/**
	 * Cached user throttle collections within the interval.
	 *
	 * @var \Illuminate\Database\Eloquent\Collection
	 */
	protected $userThrottles = array();

	/**
	 * Create a new Illuminate throttle repository.
	 *
	 * @param  string  $model
	 * @param  int  $globalInterval
	 * @param  int|array  $globalThresholds
	 * @param  int  $ipInterval
	 * @param  int|array  $ipThresholds
	 * @param  int  $userInterval
	 * @param  int|array  $userThresholds
	 */
	public function __construct($model = null, $globalInterval = null, $globalThresholds = null, $ipInterval = null, $ipThresholds = null, $userInterval = null, $userThresholds = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}

		if (isset($globalInterval))
		{
			$this->globalInterval = (int) $globalInterval;
		}

		if (isset($globalThresholds))
		{
			$this->globalThresholds = is_array($globalThresholds) ? $globalThresholds : (int) $globalThresholds;
		}

		if (isset($ipInterval))
		{
			$this->ipInterval = (int) $ipInterval;
		}

		if (isset($ipThresholds))
		{
			$this->ipThresholds = is_array($ipThresholds) ? $ipThresholds : (int) $ipThresholds;
		}

		if (isset($userInterval))
		{
			$this->userInterval = (int) $userInterval;
		}

		if (isset($userThresholds))
		{
			$this->userThresholds = is_array($userThresholds) ? $userThresholds : (int) $userThresholds;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function globalDelay()
	{
		return $this->delay('global');
	}

	/**
	 * {@inheritDoc}
	 */
	public function ipDelay($ipAddress)
	{
		return $this->delay('ip', $ipAddress);
	}

	/**
	 * {@inheritDoc}
	 */
	public function userDelay(UserInterface $user)
	{
		return $this->delay('user', $user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function log($ipAddress, UserInterface $user = null)
	{
		$global = $this->createModel();
		$global->fill(array(
			'type' => 'global',
		));
		$global->save();

		$ip = $this->createModel();
		$ip->fill(array(
			'type' => 'ip',
			'ip' => $ipAddress,
		));
		$ip->save();

		$userThrottle = $this->createModel();
		$userThrottle->fill(array(
			'type' => 'user',
		));
		$userThrottle->user_id = $user->getUserId();
		$userThrottle->save();
	}

	/**
	 * Returns a delay for the given type.
	 *
	 * @param  string  $type
	 * @param  mixed   $argument
	 * @return int
	 */
	protected function delay($type, $argument = null)
	{
		// Based on the given type, we will generate method and property names
		$method     = 'get'.studly_case($type).'Throttles';
		$thresholds = $type.'Thresholds';
		$interval   = $type.'Interval';

		$throttles = $this->$method($argument);
		if ( ! $throttles->count()) return;

		$throttle = $throttles->first();

		if (is_array($this->$thresholds))
		{
			foreach (array_reverse($this->$thresholds, true) as $attempts => $delay)
			{
				if ($throttles->count() > $attempts)
				{
					return $delay;
				}
			}
		}
		elseif ($throttles->count() > $this->$thresholds)
		{
			return $this->secondsToFree($throttle, $this->$thresholds, $this->$interval);
		}
	}

	/**
	 * Gets the global throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function getGlobalThrottles()
	{
		if ($this->globalThrottles === null)
		{
			$this->globalThrottles = $this->loadGlobalThrottles();
		}

		return $this->globalThrottles;
	}

	/**
	 * Loads and returns the global throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function loadGlobalThrottles()
	{
		$interval = Carbon::now()
			->subSeconds($this->globalInterval);

		return $this->createModel()
			->newQuery()
			->where('type', '=', 'global')
			->where('created_at', '>', $interval)
			->get();
	}

	/**
	 * Gets the IP address throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function getIpThrottles($ipAddress)
	{
		if ( ! array_key_exists($ipAddress, $this->ipThrottles))
		{
			$this->ipThrottles[$ipAddress] = $this->loadIpThrottles($ipAddress);
		}

		return $this->ipThrottles[$ipAddress];
	}

	/**
	 * Loads and returns the IP address throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function loadIpThrottles($ipAddress)
	{
		$interval = Carbon::now()
			->subSeconds($this->ipInterval);

		return $this
			->createModel()
			->where('type', '=', 'ip')
			->where('ip', '=', $ipAddress)
			->where('created_at', '>', $interval)
			->get();
	}

	/**
	 * Gets the user throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function getUserThrottles(UserInterface $user)
	{
		$key = $user->getUserId();

		if ( ! array_key_exists($key, $this->userThrottles))
		{
			$this->userThrottles[$key] = $this->loadUserThrottles($user);
		}

		return $this->userThrottles[$key];
	}

	/**
	 * Loads and returns the user throttles collection.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function loadUserThrottles(UserInterface $user)
	{
		$interval = Carbon::now()
			->subSeconds($this->userInterval);

		return $this
			->createModel()
			->where('type', '=', 'user')
			->where('user_id', '=', $user->getUserId())
			->where('created_at', '>', $interval)
			->get();
	}

	/**
	 * Returns the seconds to free based on the given throttle
	 * and the presented delay in seconds, by comparing it to
	 * now.
	 *
	 * @param  \Cartalyst\Sentry\Throttling\EloquentThrottle  $throttle
	 * @param  int  $delay
	 * @return int
	 */
	protected function secondsToFree(EloquentThrottle $throttle, $delay, $interval)
	{
		$free = $throttle
			->created_at
			->addSeconds($interval);

		return $free->diffInSeconds();
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
