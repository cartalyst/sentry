<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\ThrottleInterface;
use DateTime;

class Throttle extends EloquentModel implements ThrottleInterface
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'throttle';

	/**
	 * Current Login Throttle Boject
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
		$this->limit = $limit;
	}

	/**
	 * Set Suspension Time
	 *
	 * @param   string  $minutes
	 */
	public function setSuspensionTime($minutes)
	{
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
		if ( ! $this->check($login))
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
		return $this->unsuspend($login);
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
		$this->current->suspended = 0;
		$this->current->suspended_at = null;

		return $this->current->save();
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

	/**
	 * Check Throttle Status
	 *
	 * @param   string  $Login
	 * @return  bool
	 */
	public function check($login)
	{
		$this->findByLogin($login);

		// check if the user is banned
		if ($this->current->banned)
		{
			return false;
		}

		// check if the user is suspended
		if ($this->current->suspended)
		{
			$suspended = new DateTime($this->current->suspended_at);
			$unsuspend_at = $suspended->modify('+'.$this->time.' minutes');
			$now = new DateTime();

			if ($unsuspend_at <= $now)
			{
				$this->unsuspend($this->current->login);

				return true;
			}
		}

		if ($this->getAttempts($login) >= $this->limit)
		{
			return false;
		}

		return true;
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