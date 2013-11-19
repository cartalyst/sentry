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

class IlluminateThrottleRepository implements ThrottleRepositoryInterface {

	/**
	 * The user's IP address.
	 *
	 * @var string
	 */
	protected $ipAddress;

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
	 * Create a new Illuminate throttle repository.
	 *
	 * @param  string  $ipAddress
	 * @param  int  $globalInterval
	 * @param  int|array  $globalThresholds
	 * @param  int  $ipInterval
	 * @param  int|array  $ipThresholds
	 */
	public function __construct($ipAddress, $globalInterval = null, $globalThresholds = null, $ipInterval = null, $ipThresholds = null)
	{
		$this->ipAddress = $ipAddress;

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
	}

	/**
	 * {@inheritDoc}
	 */
	public function globalDelay()
	{
		$interval = $this->globalIntervalDateTime();

		$throttles = EloquentThrottle::where('created_at', '>', $interval)
			->get();

		if ( ! $throttles->count())
		{
			return;
		}

		$throttle = $throttles->first();

		if (is_array($this->globalThresholds))
		{
			foreach (array_reverse($this->globalThresholds, true) as $attempts => $delay)
			{
				if ($throttles->count() >= $attempts)
				{
					return $delay;
				}
			}
		}
		elseif ($this->globalThresholds >= $throttles->count())
		{
			return $this->secondsToFree($throttle, $this->globalThresholds);
		}
	}

	/**
	 * Returns a DateTime object, set back by the global interval.
	 *
	 * @return \Carbon\Carbon
	 */
	protected function globalIntervalDateTime()
	{
		$now = Carbon::now();
		return $now->subSeconds($this->globalInterval);
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
	protected function secondsToFree(EloquentThrottle $throttle, $delay)
	{
		$free = $throttle
			->created_at
			->addSeconds($this->globalInterval);

		return $free->diffInSeconds();
	}

}
