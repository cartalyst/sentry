<?php
/**
 * Part of the Sentry bundle for Laravel.
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
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

use Config;
use DB;

/**
 * Sentry Auth Attempt Class
 */

class SentryAttemptsException extends SentryException {}
class SentryUserSuspendedException extends SentryAttemptsException {}

class Sentry_Attempts
{

	/**
	 * @var  string  Database instance
	 */
	protected static $db_instance = null;

	/**
	 * @var  string  Suspension table name
	 */
	protected static $table_suspend = null;

	/**
	 * @var  array  Stores suspension/limit config data
	 */
	protected static $limit = array();

	/**
	 * @var  string  Login id
	 */
	protected $login_id = null;

	/**
	 * @var  string  IP address
	 */
	protected $ip_address = null;

	/**
	 * @var  int  Number of login attempts
	 */
	protected $attempts = null;

	/**
	 * Attempts Constructor
	 *
	 * @param   string  user login
	 * @param   string  ip address
	 * @return  Sentry_Attempts
	 * @throws  SentryAttemptsException
	 */
	public function __construct($login_id = null, $ip_address = null)
	{
		$_db_instance = trim(Config::get('sentry::sentry.db_instance'));

		// db_instance check
		if ( ! empty($_db_instance) )
		{
			static::$db_instance = $_db_instance;
		}

		static::$table_suspend = Config::get('sentry::sentry.table.users_suspended');
		static::$limit = array(
			'enabled' => Config::get('sentry::sentry.limit.enabled'),
			'attempts' => Config::get('sentry::sentry.limit.attempts'),
			'time' => Config::get('sentry::sentry.limit.time')
		);
		$this->login_id = $login_id;
		$this->ip_address = $ip_address;

		// limit checks
		if (static::$limit['enabled'] === true)
		{
			if ( ! is_int(static::$limit['attempts']) or static::$limit['attempts'] <= 0)
			{
				throw new SentryConfigException(__('sentry::sentry.invalid_limit_attempts'));
			}

			if ( ! is_int(static::$limit['time']) or static::$limit['time'] <= 0)
			{
				throw new SentryConfigException(__('sentry::sentry.invalid_limit_time'));
			}
		}

		$query = DB::connection(static::$db_instance)
			->table(static::$table_suspend);

		if ($this->login_id)
		{
			$query = $query->where('login_id', '=', $this->login_id);
		}

		if ($this->ip_address)
		{
			$query = $query->where('ip', '=', $this->ip_address);
		}

		$result = $query->get();

		foreach ($result as &$row)
		{
			$row = get_object_vars($row);
			// check if last attempt was more than 15 min ago - if so reset counter
			if ($row['last_attempt_at'] and ($row['last_attempt_at'] + static::$limit['time'] * 60) <= time())
			{
				$this->clear($row['login_id'], $row['ip']);
				$row['attempts'] = 0;
			}

			// check unsuspended time and clear if time is > than it
			if ($row['unsuspend_at'] and $row['unsuspend_at'] <= time())
			{
				$this->clear($row['login_id'], $row['ip']);
				$row['attempts'] = 0;
			}
		}

		if (count($result) > 1)
		{
			$this->attempts = $result;
		}
		elseif ($result)
		{
			$this->attempts = $result[0]['attempts'];
		}
		else
		{
			$this->attempts = 0;
		}

	}

	/**
	 * Check Number of Login Attempts
	 *
	 * @return  int
	 */
	public function get()
	{
		return $this->attempts;
	}

	/**
	 * Gets attempt limit number
	 *
	 * @return  int
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
	public function add()
	{
		// make sure a login id and ip address are set
		if (empty($this->login_id) or empty($this->ip_address))
		{
			throw new SentryAttemptsException(__('sentry::sentry.login_ip_required'));
		}

		// this shouldn't happen, but put it just to make sure
		if (is_array($this->attempts))
		{
			throw new SentryAttemptsException(__('sentry::sentry.single_user_required'));
		}

		if ($this->attempts)
		{
			DB::connection(static::$db_instance)
				->table(static::$table_suspend)
				->where('login_id', '=', $this->login_id)
				->where('ip', '=', $this->ip_address)
				->update(array(
					'attempts' => ++$this->attempts,
					'last_attempt_at' => time(),
				));
		}
		else
		{
			DB::connection(static::$db_instance)
				->table(static::$table_suspend)
				->insert(array(
					'login_id' => $this->login_id,
					'ip' => $this->ip_address,
					'attempts' => ++$this->attempts,
					'last_attempt_at' => time(),
				));
		}
	}

	/**
	 * Clear Login Attempts
	 *
	 * @param string
	 * @param string
	 */
	public function clear()
	{
		$query = DB::connection(static::$db_instance)
			->table(static::$table_suspend);

		if ($this->login_id)
		{
			$query = $query->where('login_id', '=', $this->login_id);
		}

		if ($this->ip_address)
		{
			$query = $query->where('ip', '=', $this->ip_address);
		}

		$result = $query->delete();
		$this->attempts = 0;
	}

	/**
	 * Suspend
	 *
	 * @param string
	 * @param int
	 */
	public function suspend()
	{
		if (empty($this->login_id) or empty($this->ip_address))
		{
			throw new SentryUserSuspendedException(__('sentry::sentry.login_ip_required'));
		}

		// only updates table if unsuspended at has no value
		$result = DB::connection(static::$db_instance)
			->table(static::$table_suspend)
			->where('login_id', '=', $this->login_id)
			->where('ip', '=', $this->ip_address) //\Input::real_ip()
			->where('unsuspend_at', '=', null)
			->or_where('unsuspend_at', '=', 0)
			->update(array(
				'suspended_at' => time(),
				'unsuspend_at' => time()+(static::$limit['time'] * 60),
			));

		throw new SentryUserSuspendedException(
			__('sentry::sentry.user_suspended', array('account' => $this->login_id, 'time' => static::$limit['time']))
		);
	}

}
