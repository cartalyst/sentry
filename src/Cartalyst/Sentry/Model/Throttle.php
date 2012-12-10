<?php namespace Cartalyst\Sentry\Model;
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

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\ThrottleInterface;
use Cartalyst\Sentry\UserSuspendedException;
use Cartalyst\Sentry\UserBannedException;
use Cartalyst\Sentry\ThrottleLimitException;
use Cartalyst\Sentry\ThrottleTimeException;
use DateTime;

class Throttle extends EloquentModel implements ThrottleInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'throttle';

	/**
	 * Current login throttle object
	 *
	 * @var Throttle
	 */
	protected $current;

	/**
	 * Attempt limit
	 *
	 * @var int
	 */
	protected $limit = 5;

	/**
	 * Suspensions time in minutes
	 *
	 * @var int
	 */
	protected $time = 15;

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Throttling status
	 *
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * Set Attempt Limit
	 *
	 * @param  string  $Login
	 */
	public function setAttemptLimit($limit)
	{
		if ( ! is_int($limit) and ! is_null($limit))
		{
			throw new ThrottleLimitException;
		}

		$this->limit = $limit;
	}

	/**
	 * Get Attempt Limit
	 *
	 * @param   string  $Login
	 * @return  int
	 */
	public function getAttemptLimit()
	{
		return $this->limit;
	}

	/**
	 * Set Suspension Time
	 *
	 * @param  string  $minutes
	 */
	public function setSuspensionTime($minutes)
	{
		if ( ! is_int($minutes) and ! is_null($minutes))
		{
			throw new ThrottleTimeException;
		}

		$this->time = $minutes;
	}

	/**
	 * Get Suspension Time
	 *
	 * @param   string  $Login
	 * @return  int
	 */
	public function getSuspensionTime()
	{
		return $this->time;
	}

	/**
	 * Enable throttling
	 *
	 * @return void
	 */
	public function enable()
	{
		$this->enabled = true;
	}

	/**
	 * Disable throttling
	 *
	 * @return void
	 */
	public function disable()
	{
		$this->enabled = false;
	}

	/**
	 * Check if throttle is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Get Login Attempts
	 *
	 * @param  string  $Login
	 * @return int
	 */
	public function getAttempts($login)
	{
		$this->findByLogin($login);

		if ($this->current->last_attempt_at)
		{
			if ( ! is_string($this->current->last_attempt_at))
			{
				$this->current->last_attempt_at = $this->current->last_attempt_at->format('Y-m-d H:i:s');
			}
			$clearTime = new DateTime($this->current->last_attempt_at);
			$clearAt = $clearTime->modify('+'.$this->time.' minutes');
			$now = new DateTime();

			if ($clearAt <= $now)
			{
				$this->current->attempts = 0;
			}

			unset($clearTime);
			unset($clearAt);
			unset($now);
		}

		return $this->current->attributes['attempts'];
	}

	/**
	 * Add Login Attempt
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function addAttempt($login)
	{
		$this->findByLogin($login);

		$this->current->attempts = $this->getAttempts($login) + 1;
		$this->current->last_attempt_at = $this->freshTimeStamp();

		// if they fail the check now, suspend them
		if ($this->getAttempts($login) >= $this->limit)
		{
			return $this->suspend($login);
		}

		return $this->current->save();
	}

	/**
	 * Clear Login Attempts
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function clearAttempts($login)
	{
		$this->findByLogin($login);

		if ($this->getAttempts($login) === 0 and $this->current->suspended === 0)
		{
			return true;
		}

		$this->current->attempts = 0;
		$this->current->last_attempt_at = null;
		$this->current->suspended = 0;
		$this->current->suspended_at = null;

		return $this->current->save();
	}

	/**
	 * Suspend a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function suspend($login)
	{
		$this->findByLogin($login);

		if ($this->current->suspended)
		{
			return true;
		}

		$this->current->suspended = 1;
		$this->current->suspended_at = $this->freshTimestamp();
		return $this->current->save();
	}

	/**
	 * Unsuspend a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function unsuspend($login)
	{
		$this->findByLogin($login);

		if ( ! $this->current->suspended)
		{
			return true;
		}

		$this->current->attempts = 0;
		$this->current->last_attempt_at = null;
		$this->current->suspended = 0;
		$this->current->suspended_at = null;

		return $this->current->save();
	}

	public function isSuspended($login)
	{
		$this->login = $this->findByLogin($login);

		// check if the user is suspended
		if ($this->current->suspended)
		{
			$suspended = new DateTime($this->current->suspended_at);
			$unsuspendAt = $suspended->modify('+'.$this->time.' minutes');
			$now = new DateTime();

			if ($unsuspendAt <= $now)
			{
				$this->unsuspend($this->current->login);

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
	 * Ban a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function ban($login)
	{
		$this->login = $this->findByLogin($login);

		$this->current->banned = 1;

		return $this->current->save();
	}

	/**
	 * Unban a login
	 *
	 * @param  string  $Login
	 * @return bool
	 */
	public function unban($login)
	{
		$this->login = $this->findByLogin($login);

		if ( ! $this->current->banned)
		{
			return true;
		}

		$this->current->banned = 0;

		return $this->current->save();
	}

	public function isBanned($login)
	{
		$this->login = $this->findByLogin($login);

		// check if the user is banned
		if ($this->current->banned)
		{
			return true;
		}

		return false;
	}

	public function check($login)
	{
		if ($this->isBanned($login))
		{
			throw new UserBannedException;
		}

		if ($this->isSuspended($login))
		{
			throw new UserSuspendedException;
		}

		return true;
	}

	/**
	 * Find and set Throttle by login or get a new instance
	 *
	 * @param  string  $login
	 */
	protected function findByLogin($login)
	{
		if ($this->current and $this->current->login == $login)
		{
			return $this->current;
		}

		$current = $this->where('login', '=', $login)->first();

		$this->current = ($current) ?: $this->newInstance(array(
			'login'    => $login,
			'attempts' => 0
		));
	}

}