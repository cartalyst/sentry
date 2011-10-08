<?php

/**
 * Sentry Auth Attempt Class
 *
 * @author Daniel Petrie
 */

namespace Sentry;

class SentryUserSuspendedException extends \Fuel_Exception {}

class Sentry_Attempts
{

	protected static $table_suspend = null;

	protected static $limit = array();

	public function __construct()
	{
		\Config::load('sentry', true);

		static::$table_suspend = \Config::get('sentry.table.users_suspended');
		static::$limit = array(
			'enabled' => \Config::get('sentry.limit.enabled'),
			'attempts' => \Config::get('sentry.limit.attempts'),
			'time' => \Config::get('sentry.limit.time')
		);

		// limit checks
		if (static::$limit['enabled'] === true)
		{
			if ( ! is_int(static::$limit['attempts']) or static::$limit['attempts'] <= 0)
			{
				throw new \SentryAuthConfigException(
					'Sentry Config Item: "limit.attempts" must be an integer greater than 0');
			}

			if ( ! is_int(static::$limit['time']) or static::$limit['time'] <= 0)
			{
				throw new \SentryAuthConfigException(
					'Sentry Config Item: "limit.time" must be an integer greater than 0');
			}
		}
	}

	/**
	 * Check Number of Login Attempts
	 *
	 * @param string
	 */
	public function get($login_id)
	{
		$result = \DB::select('attempts', 'last_attempt_at', 'unsuspend_at')
			->from(static::$table_suspend)
			->where('login_id', $login_id)
			->where('ip', \Input::real_ip())
			->execute()
			->current();

		// check if last attempt was more than 15 min ago - if so reset counter
		if ($result['last_attempt_at']
			and ($result['last_attempt_at'] + static::$limit['time'] * 60) <= time())
		{
			$this->clear($login_id);
			return 0;
		}

		// check unsuspended time and clear if time is > than it
		if ($result['unsuspend_at'] and $result['unsuspend_at'] <= time())
		{
			$this->clear($login_id);
			return 0;
		}

		return $result['attempts'];
	}

	/**
	 * Gets attempt limit number
	 */

	 public function get_limit()
	 {
	 	return static::$limit['attempts'];
	 }

	/**
	 * Add Login Attempt
	 *
	 * @param string
	 * @param int
	 */
	public function add($login_id, $attempts = null)
	{
		if ($attempts)
		{
			$result = \DB::update(static::$table_suspend)
				->set(array(
					'attempts' => $attempts + 1,
					'last_attempt_at' => time(),
				))
				->where('login_id', $login_id)
				->where('ip', \Input::real_ip())
				->execute();
		}
		else
		{
			$result = \DB::insert(static::$table_suspend)
				->set(array(
					'login_id' => $login_id,
					'ip' => \Input::real_ip(),
					'attempts' => 1,
					'last_attempt_at' => time(),
				))
				->execute();
		}
	}

	/**
	 * Clear Login Attempts
	 *
	 * @param string
	 * @param string
	 */
	public function clear($login_id, $ip = null)
	{
		if ($ip === null)
		{
			$ip = \Input::real_ip();
		}

		$result = \DB::delete(static::$table_suspend)
			->where('login_id', $login_id)
			->where('ip', $ip)
			->execute();
	}

	/**
	 * Alias of Clear_Attempts
	 *
	 * @param string
	 * @param string
	 */
	public function unsuspend($login_id, $ip = null)
	{
		$this->clear($login_id, $ip);
	}

	/**
	 * Suspend
	 *
	 * @param string
	 * @param int
	 */
	public function suspend($login_id, $timeleft = null)
	{
		// only updates table if unsuspended at has no value
		$result = \DB::update(static::$table_suspend)
			->set(array(
				'suspended_at' => time(),
				'unsuspend_at' => time()+(static::$limit['time'] * 60),
			))
			->where('login_id', $login_id)
			->where('ip', \Input::real_ip())
			->where('unsuspend_at', null)
			->execute();

		throw new \SentryUserSuspendedException(sprintf(
			'You have been suspended from trying to login into account "%s" for %s minutes.',
			$login_id, static::$limit['time']));
	}
}
