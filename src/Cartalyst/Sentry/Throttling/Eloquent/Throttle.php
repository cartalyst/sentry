<?php namespace Cartalyst\Sentry\Throttling\Eloquent;
/**
 * Part of the Sentry Package.
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
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Throttling\ThrottleInterface;
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Throttle extends Model implements ThrottleInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'throttle';

	/**
	 * Attempt limit.
	 *
	 * @var int
	 */
	protected $attemptLimit = 5;

	/**
	 * Suspensions time in minutes.
	 *
	 * @var int
	 */
	protected $suspensionTime = 15;

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Throttling status.
	 *
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * Returns the associated user with the
	 * throttler.
	 *
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUser()
	{
		return $this->user()->getResults();
	}

	/**
	 * Set attempt limit.
	 *
	 * @param  int  $limit
	 */
	public function setAttemptLimit($limit)
	{
		$this->attemptLimit = (int) $limit;
	}

	/**
	 * Get attempt limit.
	 *
	 * @return  int
	 */
	public function getAttemptLimit()
	{
		return $this->attemptLimit;
	}

	/**
	 * Set suspensin time.
	 *
	 * @param  int  $minutes
	 */
	public function setSuspensionTime($minutes)
	{
		$this->suspensionTime = (int) $_minutes;
	}

	/**
	 * Get suspension time.
	 *
	 * @param  int
	 */
	public function getSuspensionTime()
	{
		return $this->suspensionTime;
	}

	/**
	 * Get the current amount of attempts.
	 *
	 * @return int
	 */
	public function getLoginAttempts()
	{
		if ( ! is_string($this->last_attempt_at))
		{
			$this->last_attempt_at = $this->last_attempt_at->format('Y-m-d H:i:s');
		}

		$clearTime = new DateTime($this->last_attempt_at);
		$clearAt   = $clearTime->modify('+'.$this->suspensionTime.' minutes');
		$now       = new DateTime;

		if ($clearAt <= $now)
		{
			$this->attempts = 0;
		}

		unset($clearTime);
		unset($clearAt);
		unset($now);

		return $this->attempts;
	}

	/**
	 * Add a new login attempt.
	 *
	 * @return void
	 */
	public function addLoginAttempt()
	{
		$this->attempts++;
		$this->last_attempt_at = $this->freshTimeStamp();

		if ($this->getLoginAttempts() >= $this->attemptLimit)
		{
			$this->suspend();
		}
		else
		{
			$this->save();
		}
	}

	/**
	 * Clear all login attempts
	 *
	 * @return void
	 */
	public function clearLoginAttempts()
	{
		if ($this->getLoginAttempts() == 0 and ! $this->suspended)
		{
			return;
		}

		$this->attempts        = 0;
		$this->last_attempt_at = null;
		$this->suspended       = false;
		$this->suspended_at    = null;
	}

	/**
	 * Suspend the user associated with
	 * the throttle
	 *
	 * @return void
	 */
	public function suspend()
	{
		if ( ! $this->suspended)
		{
			$this->suspended    = true;
			$this->suspended_at = $this->freshTimeStamp();
			$this->save();
		}
	}

	/**
	 * Unsuspend the user.
	 *
	 * @return void
	 */
	public function unsuspend()
	{
		if ($this->suspended)
		{
			$this->attempts        = 0;
			$this->last_attempt_at = null;
			$this->suspended       = false;
			$this->suspended_at    = null;
			$this->save();
		}
	}

	/**
	 * Check if the user is suspended.
	 *
	 * @return bool
	 */
	public function isSuspended()
	{
		if ($this->suspended)
		{
			$suspended   = new DateTime($this->suspended_at);
			$unsuspendAt = $suspended->modify('+'.$this->suspensionTime.' minutes');
			$now         = new DateTime;

			if ($unsuspendAt <= $now)
			{
				$this->unsuspend();
				return false;
			}

			unset($suspended);
			unset($unsuspendAt);
			unset($now);

			return true;
		}

		return false;
	}

	/**
	 * Ban the user.
	 *
	 * @return void
	 */
	public function ban()
	{
		if ( ! $this->banned)
		{
			$this->banned = true;
			$this->save();
		}
	}

	/**
	 * Unban the user.
	 *
	 * @return void
	 */
	public function unban()
	{
		if ($this->banned)
		{
			$this->banned = false;
			$this->save();
		}
	}

	/**
	 * Check if user is banned
	 *
	 * @return bool
	 */
	public function isBanned()
	{
		return $this->banned;
	}

	/**
	 * Check user throttle status.
	 *
	 * @return bool
	 * @throws Cartalyst\Sentry\Throttling\UserBannedException
	 * @throws Cartalyst\Sentry\Throttling\UserSuspendedException
	 */
	public function check()
	{
		if ($this->isBanned())
		{
			throw new UserBannedException(sprintf(
				'User [%s] has been banned.',
				$this->getUser()->getUserLogin()
			));
		}

		if ($this->isSuspended())
		{
			throw new UserBannedException(sprintf(
				'User [%s] has been suspended.',
				$this->getUser()->getUserLogin()
			));
		}

		return true;
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
	 * User relationship for the throttle.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'user_id');
	}

	public function setLastAttemptAt($lastAttemptAt)
	{
		if ( ! $lastAttemptAt instanceof DateTime)
		{
			$lastAttemptAt = new DateTime($lastAttemptAt);
		}

		return $lastAttemptAt;
	}

	public function getLastAttemptAt($lastAttemptAt)
	{
		if ( ! $lastAttemptAt instanceof DateTime)
		{
			$lastAttemptAt = new DateTime($lastAttemptAt);
		}

		return $lastAttemptAt;
	}

	public function setSuspendedAt($suspendedAt)
	{
		if ( ! $suspendedAt instanceof DateTime)
		{
			$suspendedAt = new DateTime($suspendedAt);
		}

		return $suspendedAt;
	}

	public function getSuspendedAtAt($suspendedAt)
	{
		if ( ! $suspendedAt instanceof DateTime)
		{
			$suspendedAt = new DateTime($suspendedAt);
		}

		return $suspendedAt;
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$result = parent::toArray();

		if (isset($result['last_attempt_at']))
		{
			$result['last_attempt_at'] = $result['last_attempt_at']->format('Y-m-d H:i:s');
		}
		if (isset($result['suspended_at']))
		{
			$result['suspended_at'] = $result['suspended_at']->format('Y-m-d H:i:s');
		}

		return $result;
	}

}