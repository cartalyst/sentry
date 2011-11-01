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

use Config;
use DB;
use FuelException;
use Lang;
use Str;

class SentryUserException extends \FuelException {}
class SentryUserNotFoundException extends \SentryUserException {}

/**
 * Sentry Auth User Class
 *
 * @package  Sentry
 * @author   Daniel Petrie
 */
class Sentry_User
{
	// set class properties
	protected $user = array();
	protected $groups = array();
	protected $table = null;
	protected $join_table = null;
	protected $login_column = null;
	protected $login_column_str = '';

	/**
	 * Loads in the user object
	 *
	 * @param   int|string  User id or Login Column value
	 * @return  void
	 * @throws  SentryUserNotFoundException
	 */
	public function __construct($id = null)
	{
		// load and set config
		$this->table = strtolower(Config::get('sentry.table.users'));
		$this->join_table = strtolower(Config::get('sentry.table.users_groups'));
		$this->login_column = strtolower(Config::get('sentry.login_column'));
		$this->login_column_str = ucfirst($this->login_column);

		// if an ID was passed
		if ($id)
		{
			// make sure ID is valid
			if (is_int($id))
			{
				if ($id <= 0)
				{
					throw new \SentryUserException(__('sitrep.invalid_user_id'));
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
			$user = DB::select()
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
				throw new \SentryUserNotFoundException(__('sitrep.user_not_found'));
			}

			$groups_table = Config::get('sentry.table.groups');

			$this->groups = DB::select($groups_table.'.*')
				->from($groups_table)
				->where($this->join_table.'.user_id', '=', $this->user['id'])
				->join($this->join_table)
				->on($this->join_table.'.group_id', '=', $groups_table.'.id')
				->execute()->as_array();
		}
	}

	/**
	 * Register a user - Alias of create()
	 *
	 * @param   array  User array for creation
	 * @return  int
	 * @throws  SentryUserException
	 */
	public function register($user)
	{
		return $this->create($user, true);
	}

	/**
	 * Create's a new user.  Returns user 'id'.
	 *
	 * @param   array  User array for creation
	 * @return  int
	 * @throws  SentryUserException
	 */
	public function create(array $user, $activation = false)
	{
		// check for required fields
		if (empty($user[$this->login_column]) or empty($user['password']))
		{
			throw new \SentryUserException(
				__('sitrep.column_and_password_empty', array('column' => $this->login_column_str))
			);
		}

		// if login_column is set to username - email is still required, so check
		if ($this->login_column != 'email' and empty($user['email']))
		{
			throw new \SentryUserException(
				__('sitrep.column_email_and_password_empty', array('column' => $this->login_column_str))
			);
		}

		// check to see if login_column is already taken
		$user_exists = $this->user_exists($user[$this->login_column]);
		if (count($user_exists))
		{
			// check if account is not activated
			if ($activation and $user_exists['activated'] != 'true')
			{
				// update and resend activation code
				$this->user = $user_exists;
				$hash = \Str::random('alnum', 24);

				$update = array(
					'activation_hash' => $hash
				);

				if ($this->update($update))
				{
					return $hash;
				}

				return false;
			}

			// if login_column is not set to email - also check to make sure email doesn't exist
			if ($this->login_column != 'email' and $this->user_exists($user['email'], 'email'))
			{
				throw new \SentryUserException(__('sitrep.email_already_in_use'));
			}
			throw new \SentryUserException(
				__('sitrep.column_already_exists', array('column' => $this->login_column_str))
			);
		}

		// set new user values
		$new_user = array(
			$this->login_column => $user[$this->login_column],
			'password' => $this->generate_password($user['password']),
			'created_at' => time(),
			'activated' => ($activation) ? 'false' : 'true',
			'status' => 1,
		) + $user;

		if ($activation)
		{
			$hash = Str::random('alnum', 24);
			$new_user['activation_hash'] = $hash;

			// send email
		}

		// insert new user
		list($insert_id, $rows_affected) = DB::insert($this->table)->set($new_user)->execute();

		return ($rows_affected > 0) ? $hash : false;
	}

	/**
	 * Update the current user
	 *
	 * @param   array  Fields to update
	 * @param   bool   Whether to hash the password
	 * @return  bool
	 * @throws  SentryUserException
	 */
	public function update(array $fields, $hash_password = true)
	{
		// make sure a user id is set
		if (empty($this->user))
		{
			throw new \SentryUserException(__('sitrep.no_user_selected'));
		}

		// init update array
		$update = array();

		if (array_key_exists($this->login_column, $fields) and
		    $fields[$this->login_column] != $this->user[$this->login_column] and
		    $this->user_exists($fields[$this->login_column]))
		{
			throw new \SentryUserException(__('sitrep.column_already_exists', array('column' => $this->login_column_str)));
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

		if (array_key_exists('activation_hash', $fields))
		{
			if ( ! empty($fields['activation_hash']))
			{
				$fields['activation_hash'] = $this->generate_password($fields['activation_hash']);
			}
			$update['activation_hash'] = $fields['activation_hash'];
			unset($fields['activation_hash']);
		}

		if (array_key_exists('last_login', $fields) and
		    ! empty($fields['last_login']) and is_int($fields['last_login']))
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
		$result = DB::update($this->table)
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
	 * Delete's the current user.
	 *
	 * @return  bool
	 * @throws  SentryUserException
	 */
	public function delete()
	{
		// make sure a user id is set
		if (empty($this->user))
		{
			throw new \SentryUserException('No user is selected to delete.');
		}

		// delete user from database
		$result = DB::delete($this->table)
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
	 * Checks if the Field is set or not.
	 *
	 * @param   string  Field name
	 * @return  bool
	 */
	public function __isset($field)
	{
		return array_key_exists($field, $this->user);
	}

	/**
	 * Gets a field value of the user
	 *
	 * @param   string  Field name
	 * @return  mixed
	 * @throws  SentryUserException
	 */
	public function __get($field)
	{
		return $this->get($field);
	}

	/**
	 * Gets a given field (or array of fields).
	 *
	 * @param   string|array  Field(s) to get
	 * @return  mixed
	 * @throws  SentryUserException
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
					throw new \SentryUserException(sprintf('"%s" does not exist in "user" object.', $key));
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

			throw new \SentryUserException(sprintf('"%s" does not exist in "user" object.', $field));
		}
	}

	/**
	 * Changes a user's password
	 *
	 * @param   string  The new password
	 * @param   string  Users old password
	 * @return  bool
	 * @throws  SentryUserException
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

	/**
	 * Returns an array of groups the user is part of.
	 *
	 * @return  array
	 */
	public function groups()
	{
		return $this->groups;
	}

	/**
	 * Adds this user to the group.
	 *
	 * @param   string|int  Group ID or group name
	 * @return  bool
	 */
	public function add_to_group($id)
	{
		if ($this->in_group($id))
		{
			throw new \SentryGroupException(__('sitrep.login_column_empty', array('group' => $id)));
		}

		$field = 'name';
		if (is_numeric($id))
		{
			$field = 'id';
		}

		$group = new \Sentry_Group($id);

		list($insert_id, $rows_affected) = DB::insert($this->join_table)->set(array(
			'user_id' => $this->user['id'],
			'group_id' => $group->get('id'),
		))->execute();

		return true;
	}

	/**
	 * Removes this user from the group.
	 *
	 * @param   string|int  Group ID or group name
	 * @return  bool
	 */
	public function remove_from_group($id)
	{
		if ( ! $this->in_group($id))
		{
			throw new \SentryGroupException(sprintf('User isn\'t in group "%s".', $id));
		}

		$field = 'name';
		if (is_numeric($id))
		{
			$field = 'id';
		}

		$group = new \Sentry_Group($id);

		return (bool) DB::delete($this->join_table)
				->where('user_id', $this->user['id'])
				->where('group_id', $group->get('id'))->execute();
	}

	/**
	 * Checks if the current user is part of the given group.
	 *
	 * @param   string  Group name
	 * @return  bool
	 */
	public function in_group($name)
	{
		$field = 'name';
		if (is_numeric($name))
		{
			$field = 'id';
		}
		foreach ($this->groups as $group)
		{
			if ($group[$field] == $name)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the user is an admin
	 *
	 * @return  bool
	 */
	public function is_admin()
	{
		foreach ($this->groups as $group)
		{
			if ($group['is_admin'] == 1)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the user has the given level
	 *
	 * @param   int  Level to check
	 * @return  bool
	 */
	public function has_level($level)
	{
		foreach ($this->groups as $group)
		{
			if ($group['level'] == $level)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the user has at least given level
	 *
	 * @param   int  Level to check
	 * @return  bool
	 */
	public function atleast_level($level)
	{
		foreach ($this->groups as $group)
		{
			if ($group['level'] >= $level)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if user exists already
	 *
	 * @param   string  The Login Column value
	 * @param   string  Column to use for check
	 * @return  bool
	 */
	protected function user_exists($login, $field = null)
	{
		// set field value if null
		if ($field === null)
		{
			$field = $this->login_column;
		}

		// query db to check for login_column
		$result = DB::select()
			->from($this->table)
			->where($field, $login)
			->limit(1)
			->execute()->current();

		return $result;
	}

	/**
	 * Checks the given password to see if it matches the one in the database.
	 *
	 * @param   string  Password to check
	 * @param   string  Password type
	 * @return  bool
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

	/**
	 * Generates a random salt and hashes the given password with the salt.
	 * String returned is prepended with a 16 character alpha-numeric salt.
	 *
	 * @param   string  Password to generate hash/salt for
	 * @return  string
	 */
	protected function generate_password($password)
	{
		$salt = \Str::random('alnum', 16);

		return $salt.$this->hash_password($password, $salt);
	}

	/**
	 * Hash a given password with the given salt.
	 *
	 * @param   string  Password to hash
	 * @param   string  Password Salt
	 * @return  string
	 */
	protected function hash_password($password, $salt)
	{
		$password = hash('sha256', $salt.$password);

		return $password;
	}

}
