<?php

/**
 * Part of the Sentry package for Fuel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

/**
 * Sentry Auth User Class
 *
 * @author  Daniel Petrie
 */

class SentryUserException extends \Fuel_Exception {}

class SentryUserNotFoundException extends \SentryUserException {}

class Sentry_User
{
	// set class properties
	protected $user = array();
	protected $table = null;
	protected $login_column = null;
	protected $login_column_str = '';

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
		$this->login_column = strtolower(\Config::get('sentry.login_column'));
		$this->login_column_str = ucfirst($this->login_column);

		// if an ID was passed
		if ($id)
		{
			// make sure ID is valid
			if (is_int($id))
			{
				if ($id <= 0)
				{
					throw new \SentryUserException(
								'User ID must be a valid integer greater than 0.');
				}
				// set field to id for query
				$field = 'id';
			}
			// if ID is not an integer
			else
			{
				// set field to login_column
				$field = $this->login_column;
			}

			//query database for user
			$user = \DB::select()
				->from($this->table)
				->where($field, $id)
				->execute();

			// if there was a result - update user
			if (count($user))
			{
				$this->user = $user->current();
			}
			// user doesn't exist
			else
			{
				throw new \SentryUserNotFoundException('User does not exist.');
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
		// make sure user param is an array
		if ( ! is_array($user))
		{
			throw new \SentryUserException('Create/Register paramater must be an array.');
		}

		// check for required fields
		if (empty($user[$this->login_column]) or empty($user['password']))
		{
			throw new \SentryUserException(sprintf('%s and Password can not be empty.', $this->login_column_str));
		}

		// if login_column is set to username - email is still required, so check
		if ($this->login_column != 'email' and empty($user['email']))
		{
			throw new \SentryUserException(sprintf('%s, Email and Password can not be empty.', $this->login_column_str));
		}

		// check to see if login_column is already taken
		if ($this->user_exists($user[$this->login_column]))
		{
			// if login_column is not set to email - also check to make sure email doesn't exist
			if ($this->login_column != 'email' and $this->user_exists($user['email'], 'email'))
			{
				throw new \SentryUserException('Email already exists.');
			}
			throw new \SentryUserException(sprintf('%s already exists.', $this->login_column_str));
		}

		// set new user values
		$new_user = array(
			$this->login_column => $user[$this->login_column],
			'password' => $this->generate_password($user['password']),
			'created_at' => time(),
			'status' => 1,
		) + $user;

		// insert new user
		list($insert_id, $rows_affected) = \DB::insert($this->table)->set($new_user)->execute();

		return ($rows_affected > 0) ? $insert_id : false;

	}

	/**
	 * Update User
	 *
	 * @param array
	 * @param bool
	 */
	public function update($fields, $hash_password = true)
	{
		// make sure a user id is set
		if (empty($this->user))
		{
			throw new \SentryUserException('No user is selected to update.');
		}

		// make sure fields is an array
		if ( ! is_array($fields))
		{
			throw new \SentryUserException('Update param must be an array');
		}

		// init update array
		$update = array();

		if (array_key_exists($this->login_column, $fields) and
		    $fields[$this->login_column] != $this->user[$this->login_column] and
		    $this->user_exists($fields[$this->login_column]))
		{
			throw new \SentryUserException(sprintf('%s already exists.', $this->login_column_str));
		}
		elseif (array_key_exists($this->login_column, $fields) and
		        $fields[$this->login_column] == '')
		{
			throw new \SentryUserException(sprintf('%s must not be blank.', $this->login_column_str));
		}
		elseif (array_key_exists($this->login_column, $fields))
		{
			$update[$this->login_column] = $fields[$this->login_column];
			unset($fields[$this->login_column]);
		}

		// if updating email
		if (array_key_exists('email', $fields) and
		    $fields['email'] != $this->user['email'])
		{
			// make sure email does not already exist
			if ($this->user_exists($fields['email'], 'email'))
			{
				throw new \SentryUserException('Email already exists.');
			}
			$update['email'] = $fields['email'];
			unset($fields['email']);
		}

		// update password
		if (array_key_exists('password', $fields))
		{
			if (empty($fields['password']))
			{
				throw new \SentryUserException('Password must not be blank.');
			}
			if ($hash_password)
			{
				$fields['password'] = $this->generate_password($fields['password']);
			}
			$update['password'] = $fields['password'];
			unset($fields['password']);
		}

		// update temp password
		if (array_key_exists('temp_password', $fields))
		{
			if ( ! empty($fields['temp_password']))
			{
				$fields['temp_password'] = $this->generate_password($fields['temp_password']);
			}
			$update['temp_password'] = $fields['temp_password'];
			unset($fields['temp_password']);
		}

		// update password reset hash
		if (array_key_exists('password_reset_hash', $fields))
		{
			if ( ! empty($fields['password_reset_hash']))
			{
				$fields['password_reset_hash'] = $this->generate_password($fields['password_reset_hash']);
			}
			$update['password_reset_hash'] = $fields['password_reset_hash'];
			unset($fields['password_reset_hash']);
		}

		// update remember me cookie hash
		if (array_key_exists('remember_me', $fields))
		{
			if ( ! empty($fields['remember_me']))
			{
				$fields['remember_me'] = $this->generate_password($fields['remember_me']);
			}
			$update['remember_me'] = $fields['remember_me'];
			unset($fields['remember_me']);
		}

		if (array_key_exists('last_login', $fields)
				and ! empty($fields['last_login']) and is_int($fields['last_login']))
		{
			$update['last_login'] = $fields['last_login'];
			unset($fields['last_login']);
		}

		$update += $fields;

		if (empty($update))
		{
			return true;
		}

		// add update time
		$update['updated_at'] = time();

		// update database
		$result = \DB::update($this->table)
			->set($update)
			->where('id', $this->user['id'])
			->execute();

		if ($result)
		{
			// change user values in object
			$this->user = $update + $this->user;

			return true;
		}

		return false;
	}

	/**
	 * Delete User
	 */
	public function delete()
	{
		// make sure a user id is set
		if (empty($this->user))
		{
			throw new \SentryUserException('No user is selected to delete.');
		}

		// delete user from database
		$result = \DB::delete($this->table)
			->where('id', $this->user['id'])
			->execute();

		// if user was deleted
		if ($result)
		{
			// update user to null
			$this->user = array();

			return true;
		}

		return false;
	}

	/**
	 * Get User Data
	 *
	 * @param string, array
	 */
	public function __isset($field)
	{
		return array_key_exists($field, $this->user);
	}

	/**
	 * Get User Data
	 *
	 * @param string, array
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Get User Data
	 *
	 * @param string, array
	 */
	public function get($field = null)
	{
		// make sure a user id is set
		if (empty($this->user['id']))
		{
			throw new \SentryUserException('No user is selected to get.');
		}

		// if no fields were passed - return entire user
		if ($field === null)
		{
			return $this->user;
		}
		// if field is an array - return requested fields
		else if (is_array($field))
		{
			$values = array();

			// loop through requested fields
			foreach ($field as $key)
			{
				// check to see if field exists in user
				if (array_key_exists($key, $this->user))
				{
					$values[$key] = $this->user[$key];
				}
				else
				{
					throw new \SentryUserException(
								sprintf('"%s" does not exist in "user" object.', $key));
				}
			}

			return $values;
		}
		// if single field was passed - return its value
		else
		{
			// check to see if field exists in user
			if (array_key_exists($field, $this->user))
			{
				return $this->user[$field];
			}

			throw new \SentryUserException(
						sprintf('"%s" does not exist in "user" object.', $field));
		}
	}

	/**
	 * Change Password
	 *
	 * @param string
	 * @param string
	 */
	public function change_password($password, $old_password)
	{
		// make sure old password matches the current password
		if ( ! $this->check_password($old_password))
		{
			throw new \SentryUserException('Old password is invalid');
		}
		return $this->update(array('password' => $password));
	}


	/** Acl methods if needed **/



	/** Helpers **/

	/**
	 * Check if user exists already
	 *
	 * @param string
	 */
	protected function user_exists($login, $field = null)
	{
		// set field value if null
		if ($field === null)
		{
			$field = $this->login_column;
		}

		// query db to check for login_column
		$result = \DB::select()
			->from($this->table)
			->where($field, $login)
			->limit(1)
			->execute();

		return count($result);
	}

	/**
	 * Check Password
	 *
	 * @param string
	 * @param string
	 */
	public function check_password($password, $field = 'password')
	{
		// grabs the salt from the current password
		$salt = substr($this->user[$field], 0, 16);

		// hash the inputted password
		$password = $salt.$this->hash_password($password, $salt);

		// check to see if passwords match
		return $password == $this->user[$field];
	}

	protected function generate_password($password)
	{
		$salt = \Str::random('alnum', 16);

		return $salt.$this->hash_password($password, $salt);
	}

	protected function hash_password($password, $salt)
	{
		$password = hash('sha256', $salt.$password);

		return $password;
	}

}
