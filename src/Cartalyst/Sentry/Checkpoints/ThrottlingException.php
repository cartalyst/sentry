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

use Carbon\Carbon;
use RuntimeException;

class ThrottlingException extends RuntimeException {

	/**
	 * Delay, in seconds.
	 *
	 * @var string
	 */
	protected $delay;

	/**
	 * Throttling type which caused the exception.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Get the delay.
	 *
	 * @return int
	 */
	public function getDelay()
	{
		return $this->delay;
	}

	/**
	 * Set the delay.
	 *
	 * @param  int  $delay
	 * @return void
	 */
	public function setDelay($delay)
	{
		$this->delay = $delay;
	}

	/**
	 * Get the type.
	 *
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the type.
	 *
	 * @param  int  $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * Get a Carbon object representing the time which the throttle is lifted.
	 *
	 * @return \Carbon\Carbon
	 */
	public function getFree()
	{
		return Carbon::now()->addSeconds($this->delay);
	}

}
