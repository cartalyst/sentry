<?php namespace Cartalyst\Sentry\Throttling\Eloquent;
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
use Cartalyst\Sentry\Throttling\UserSuspendedException;
use Cartalyst\Sentry\Throttling\UserBannedException;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Throttle extends Model implements ThrottleInterface {

	/**
	 * Throttling status.
	 *
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'throttle';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array();

	/**
	 * Attempt limit.
	 *
	 * @var int
	 */
	protected static $attemptLimit = 5;

	/**
	 * Suspensions time in minutes.
	 *
	 * @var int
	 */
	protected static $suspensionTime = 15;

	/**
	 * Returns the associated user with the throttler.
	 *
	 * @return \Cartalyst\Sentry\Users\UserInterface
	 */
	public function getUser()
	{
		return $this->user()->getResults();
	}

	/**
	 * Get the current amount of attempts.
	 *
	 * @return int
	 */
	public function getLoginAttempts()
	{
		if ($this->attempts > 0 and $this->last_attempt_at)
		{
			$this->clearLoginAttemptsIfAllowed();
		}

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

		if ($this->getLoginAttempts() >= static::$attemptLimit)
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
		// If our login attempts is already at zero
		// we do not need to do anything. Additionally,
		// if we are suspended, we are not going to do
		// anything either as clearing login attempts
		// makes us unsuspended. We need to manually
		// call unsuspend() in order to unsuspend.
		if ($this->getLoginAttempts() == 0 or $this->suspended)
		{
			return;
		}

		$this->attempts        = 0;
		$this->last_attempt_at = null;
		$this->suspended       = false;
		$this->suspended_at    = null;
		$this->save();
	}

	/**
	 * Suspend the user associated with the throttle
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
		if ($this->suspended and $this->suspended_at)
		{
			$this->removeSuspensionIfAllowed();
			return (bool) $this->suspended;
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
			$this->banned_at = $this->freshTimeStamp();
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
			$this->banned_at = null;
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
	 * @throws \Cartalyst\Sentry\Throttling\UserBannedException
	 * @throws \Cartalyst\Sentry\Throttling\UserSuspendedException
	 */
	public function check()
	{
		if ($this->isBanned())
		{
			throw new UserBannedException(sprintf(
				'User [%s] has been banned.',
				$this->getUser()->getLogin()
			));
		}

		if ($this->isSuspended())
		{
			throw new UserSuspendedException(sprintf(
				'User [%s] has been suspended.',
				$this->getUser()->getLogin()
			));
		}

		return true;
	}

	/**
	 * User relationship for the throttle.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'user_id');
	}

	/**
	 * Inspects the last attempt vs the suspension time
	 * (the time in which attempts must space before the
	 * account is suspended). If we can clear our attempts
	 * now, we'll do so and save.
	 *
	 * @return void
	 */
	public function clearLoginAttemptsIfAllowed()
	{
		$lastAttempt = clone $this->last_attempt_at;

		$suspensionTime  = static::$suspensionTime;
		$clearAttemptsAt = $lastAttempt->modify("+{$suspensionTime} minutes");
		$now             = new DateTime;

		if ($clearAttemptsAt <= $now)
		{
			$this->attempts = 0;
			$this->save();
		}

		unset($lastAttempt);
		unset($clearAttemptsAt);
		unset($now);
	}

	/**
	 * Inspects to see if the user can become unsuspended
	 * or not, based on the suspension time provided. If so,
	 * unsuspends.
	 *
	 * @return void
	 */
	public function removeSuspensionIfAllowed()
	{
		$suspended = clone $this->suspended_at;

		$suspensionTime = static::$suspensionTime;
		$unsuspendAt    = $suspended->modify("+{$suspensionTime} minutes");
		$now            = new DateTime;

		if ($unsuspendAt <= $now)
		{
			$this->unsuspend();
		}

		unset($suspended);
		unset($unsuspendAt);
		unset($now);
	}

	/**
	 * Get mutator for the suspended property.
	 *
	 * @param  mixed  $suspended
	 * @return bool
	 */
	public function getSuspendedAttribute($suspended)
	{
		return (bool) $suspended;
	}

	/**
	 * Get mutator for the banned property.
	 *
	 * @param  mixed  $banned
	 * @return bool
	 */
	public function getBannedAttribute($banned)
	{
		return (bool) $banned;
	}

	/**
	 * Get the attributes that should be converted to dates.
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array_merge(parent::getDates(), array('last_attempt_at', 'suspended_at', 'banned_at'));
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$result = parent::toArray();

		if (isset($result['suspended']))
		{
			$result['suspended'] = $this->getSuspended($result['suspended']);
		}
		if (isset($result['banned']))
		{
			$result['banned'] = $this->getBanned($result['banned']);
		}
		if (isset($result['last_attempt_at']) and $result['last_attempt_at'] instanceof DateTime)
		{
			$result['last_attempt_at'] = $result['last_attempt_at']->format('Y-m-d H:i:s');
		}
		if (isset($result['suspended_at']) and $result['suspended_at'] instanceof DateTime)
		{
			$result['suspended_at'] = $result['suspended_at']->format('Y-m-d H:i:s');
		}

		return $result;
	}

	/**
	 * Set attempt limit.
	 *
	 * @param  int  $limit
	 */
	public static function setAttemptLimit($limit)
	{
		static::$attemptLimit = (int) $limit;
	}

	/**
	 * Get attempt limit.
	 *
	 * @return  int
	 */
	public static function getAttemptLimit()
	{
		return static::$attemptLimit;
	}

	/**
	 * Set suspension time.
	 *
	 * @param  int  $minutes
	 */
	public static function setSuspensionTime($minutes)
	{
		static::$suspensionTime = (int) $minutes;
	}

	/**
	 * Get suspension time.
	 *
	 * @return  int
	 */
	public static function getSuspensionTime()
	{
		return static::$suspensionTime;
	}

}
