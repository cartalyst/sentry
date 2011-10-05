<?php

/**
 * Sentry Auth User class
 *
 * @author  Daniel Petrie
 */

namespace Sentry;

class SentryUserException extends \Fuel_Exception {}

class Sentry_User
{
	// set class properties
	protected $user = array();
	protected $table = null;
	protected $login_id = null;
	protected $required_fields = array('login_id', 'password');
	protected $optional_fields = array('password_reset_hash');

	/**
	 * Class Construtor
	 *
	 * @param int
	 */
	public function __construct($id = null)
	{
		// load and set config
		\Config::load('sentry', true);
		$this->table = \Config::get('sentry.table.users');
		$this->login_id = \Config::get('sentry.login_id');

		if ($id)
		{
			if (!is_int($id) or $id <= 0)
			{
				throw new \SentryUserException('User ID must be a valid integer greater than 0.');
			}
			else
			{
				//query database for user
				$user = \DB::select()->from($this->table)->where('id', $id)
					->execute()->as_array();

				if (count($user))
				{
					$this->user = $user;
				}
				else
				{
					throw new \SentryUserException('User ID does not exist.');
				}
			}
		}
	}

	/**
	 * Register a user - Alias of create()
	 *
	 * @param array
	 */
	public function register($user)
	{
		$this->create($user);
	}

	/**
	 * Create User
	 *
	 * @param array
	 */
	public function create($user)
	{
		// make sure $user param is an array
		if (!is_array($user))
		{
			throw new \SentryUserException('create() paramater must be an array.');
		}

		// check for required fields
		if (empty($user['login_id']) or empty($user['password']))
		{
			throw new \SentryUserException('login_id and password can not be empty.');
		}

		// check to see if login_id is already taken
		if ($this->user_exists($user['login_id']))
		{
			throw new \SentryUserException(ucfirst($this->login_id).' already exists.');
		}

		// set new user values
		$new_user = array(
			$this->login_id => $user['login_id'],
			'password' => $this->hash_password($user['password']),
			'created_at' => time(),
		);

		// insert new user
		list($insert_id, $rows_affected) = \DB::insert($this->table)->set($new_user)->execute();

		return ($rows_affected > 0) ? $insert_id : false;

	}

	public function update() {}

	public function delete() {}

	public function get() {}

	/** Acl methods **/

	/** Update Helpers **/
	protected function user_exists($login_id)
	{
		$result = \DB::select()->from($this->table)->where($this->login_id, $login_id)
					->limit(1)->execute();

		return count($result);
	}

	protected function change_password($old, $new) {}

	protected function hash_password($password)
	{
		$salt = $this->generate_salt();
		$password = hash('sha256', $password);
		return $salt.$password;
	}

	protected function generate_salt()
	{
		return \Str::random('alnum', 16);
	}

}
