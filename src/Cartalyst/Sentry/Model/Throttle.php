<?php namespace Cartalyst\Sentry\Model;

use Cartalyst\SentryException;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\ThrottleInterface;
use Cartalyst\Sentry\UserSuspendedException;
use Cartalyst\Sentry\UserBannedException;
use DateTime;

class ThrottleLimitException extends SentryException {}
class ThrottleTimeException extends SentryException {}

class Throttle extends EloquentModel implements ThrottleInterface
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'throttle';

	/**
	 * Current login throttle object
	 *
	 * @var  Throttle
	 */
	protected $current;

	/**
	 * Attempt limit
	 *
	 * @var  int
	 */
	protected $limit = 5;

	/**
	 * Suspensions time in minutes
	 *
	 * @var  int
	 */
	protected $time = 15;

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Set Attempt Limit
	 *
	 * @param   string  $Login
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
	 * Set Suspension Time
	 *
	 * @param   string  $minutes
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
	 * Get Login Attempts
	 *
	 * @param   string  $Login
	 * @return  int
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
			$clear_at = $clearTime->modify('+'.$this->time.' minutes');
			$now = new DateTime();

			if ($clear_at <= $now)
			{
				$this->current->attempts = 0;
			}

			unset($clearTime);
			unset($clear_at);
			unset($now);
		}

		return $this->current->attributes['attempts'];
	}

	/**
	 * Add Login Attempt
	 *
	 * @param   string  $Login
	 * @return  bool
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
	 * @param   string  $Login
	 * @return  bool
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
	 * @param   string  $Login
	 * @return  bool
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
	 * @param   string  $Login
	 * @return  bool
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
			$unsuspend_at = $suspended->modify('+'.$this->time.' minutes');
			$now = new DateTime();

			if ($unsuspend_at <= $now)
			{
				$this->unsuspend($this->current->login);

				return false;
			}

			unset($suspended);
			unset($unsuspend_at);
			unset($now);

			return true;
		}

		return false;
	}

	/**
	 * Ban a login
	 *
	 * @param   string  $Login
	 * @return  bool
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
	 * @param   string  $Login
	 * @return  bool
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

	/**
	 * Find and set Throttle by login or get a new instance
	 *
	 * @param   string  $login
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