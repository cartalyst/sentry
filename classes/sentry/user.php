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

	/**
	 * Class Construtor
	 *
	 * @param int
	 */
	public function __construct($id = null)
	{
		// load and set config
		\Config::load('sentry', true);
		$this->table = strtolower(\Config::get('sentry.table.users'));
		$this->login_id = strtolower(\Config::get('sentry.login_id'));

		if ($id)
		{
			if (!is_int($id) or $id <= 0)
			{
				throw new \SentryUserException('User ID must be a valid integer greater than 0.');
			}
			else
			{
				//query database for user
				$user = \DB::select()
					->from($this->table)
					->where('id', $id)
					->execute()
					->current();

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
			throw new \SentryUserException('Create/Register paramater must be an array.');
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

	public function update($fields)
	{
		// make sure a user id is set
		if (empty($this->user['id']))
		{
			throw new \SentryUserException('No user is selected to update.');
		}

		// make sure fields is an array
		if (!is_array($fields))
		{
			throw new \SentryUserException('Update param must be an array');
		}

		// init update array
		$update = array();

		// if updating login_id, make sure it does not exist
		if (array_key_exists('login_id', $fields))
		{
			if ($this->user_exists($fields['login_id'])
				and $fields['login_id'] != $this->user[$this->login_id]) // gracious check...
			{
				throw new \SentryUserException(ucfirst($this->login_id).' already exists.');
			}
			if (empty($fields['login_id']))
			{
				throw new \SentryUserException(ucfirst($this->login_id).' must not be blank.');
			}
			$update[$this->login_id] = $fields['login_id'];
			unset($fields['login_id']);
		}

		// update password
		if (array_key_exists('password', $fields))
		{
			if (empty($fields['password']))
			{
				throw new \SentryUserException('Password must not be blank.');
			}
			$update['password'] = $this->hash_password($fields['password']);
			unset($fields['password']);
		}

		// update password reset hash
		if (array_key_exists('password_reset', $fields))
		{
			$update['password_reset_hash'] = $this->hash_password($fields['password_reset']);
			unset($fields['password_reset']);
		}

		$update['updated_at'] = time();

		$result = \DB::update($this->table)
			->set($update)
			->where('id', $this->user['id'])
			->execute();

		if ($result > 0)
		{
			// change user values in object
			$this->user = array_merge($this->user, $update);
		}

		return $result > 0;
	}

	public function delete() {}

	public function get() {}

	/** Acl methods **/

	/** Update Helpers **/
	protected function user_exists($login_id)
	{
		$result = \DB::select()
			->from($this->table)
			->where($this->login_id, $login_id)
			->limit(1)
			->execute();

		return count($result);
	}

	protected function change_password($old, $new) {}

	protected function hash_password($password)
	{
		$salt = $this->generate_salt();
		$password = hash('sha256', $salt.$password);
		return $salt.$password;
	}

	protected function generate_salt()
	{
		return \Str::random('alnum', 16);
	}

}
