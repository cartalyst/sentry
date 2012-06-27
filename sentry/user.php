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
use Lang;
use Str;
use Request;

class SentryUserException extends SentryException {}
class SentryUserNotFoundException extends SentryUserException {}
class SentryPermissionsException extends SentryException {}

/**
 * Sentry Auth User Class
 */
class Sentry_User implements \Iterator, \ArrayAccess
{

	/**
	 * @var  string  Database instance
	 */
	protected $db_instance = null;

	/**
	 * @var  array  User
	 */
	protected $user = array();

	/**
	 * @var  array  Passwords
	 */
	protected $passwords = array();

	/**
	 * @var  array  Password Fields
	 */
	protected $password_fields = array(
		'password',
		'password_reset_hash',
		'temp_password',
		'remember_me',
		'activation_hash',
	);

	/**
	 * @var  array  Groups
	 */
	protected $groups = array();

	/**
	 * @var  object  Hashing Object
	 */
	protected $hash = null;

	/**
	 * @var  string  Table name
	 */
	protected $table = null;

	/**
	 * @var  string  User metadata table
	 */
	protected $table_metadata = null;

	/**
	 * @var  string  User groups table
	 */
	protected $table_usergroups = null;

	/**
	 * @var  string  Login column
	 */
	protected $login_column = null;

	/**
	 * @var  string  Login column string (formatted)
	 */
	protected $login_column_str = '';

	/**
	 * @var  array  Contains the merged user & group permissions
	 */
	protected $permissions = array();

	/**
	 * @var  array  Contains the rules from the sentry config
	 */
	protected $rules = array();

	/**
	 * Loads in the user object
	 *
	 * @param   int|string  User id or Login Column value
	 * @return  void
	 * @throws  SentryUserNotFoundException
	 */
	public function __construct($id = null, $check_exists = false)
	{
		// load and set config
		$this->table = strtolower(Config::get('sentry::sentry.table.users'));
		$this->table_usergroups = strtolower(Config::get('sentry::sentry.table.users_groups'));
		$this->table_metadata = strtolower(Config::get('sentry::sentry.table.users_metadata'));
		$this->login_column = strtolower(Config::get('sentry::sentry.login_column'));
		$this->login_column_str = ucfirst($this->login_column);
		$db_instance = trim(Config::get('sentry::sentry.db_instance'));

		try
		{
			// init a hashing mechanism
			$strategy = Config::get('sentry::sentry.hash.strategy');
			$options = Config::get('sentry::sentry.hash.strategies.'.$strategy);
			$this->hash = Sentry_Hash_Driver::forge($strategy, $options);
		}
		catch (SentryGroupNotFoundException $e)
		{
			throw new SentryUserException($e->getMessage());
		}

		// db_instance check
		if ( ! empty($db_instance) )
		{
			$this->db_instance = $db_instance;
		}

		// if an ID was passed
		if ($id)
		{
			// make sure ID is valid
			if (is_int($id))
			{
				if ($id <= 0)
				{
					throw new SentryUserException(__('sentry::sentry.invalid_user_id'));
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
			$user = DB::connection($this->db_instance)
				->table($this->table)
				->where($field, '=', $id)
				->first();

			// if there was a result - update user
			if ($user !== null)
			{
				// if just a user exists check - return true, no need for additional queries
				if ($check_exists)
				{
					return true;
				}

				$temp = get_object_vars($user);

				// query for metadata
				$metadata = DB::connection($this->db_instance)
					->table($this->table_metadata)
					->where('user_id', '=', $temp['id'])
					->first();

				$temp['metadata'] = (count($metadata)) ? get_object_vars($metadata) : array();

				// lets set and remove password fields
				$temp = $this->extract_passwords($temp);

				$this->user = $temp['user'];
				$this->passwords = $temp['passwords'];
			}
			// user doesn't exist
			else
			{
				throw new SentryUserNotFoundException(__('sentry::sentry.user_not_found'));
			}

			/**
			 * fetch the user's groups and assign as array usable via $this->groups
			 */
			$groups_table = Config::get('sentry::sentry.table.groups');

			$groups = DB::connection($this->db_instance)
				->table($groups_table)
				->where($this->table_usergroups.'.user_id', '=', $this->user['id'])
				->join($this->table_usergroups,
							$this->table_usergroups.'.group_id', '=', $groups_table.'.id')
				->get($groups_table.'.*');

			foreach ($groups as &$group)
			{
				$group = get_object_vars($group);
			}
			$this->groups = $groups;

			/**
			 * set rules and permissions if enabled
			 */
			if (Config::get('sentry::sentry.permissions.enabled'))
			{
				$this->rules       = Sentry_Rules::fetch_rules();
				$this->permissions = $this->fetch_permissions();
			}
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
			throw new SentryUserException(
				__('sentry::sentry.column_and_password_empty', array('column' => $this->login_column_str))
			);
		}

		// if login_column is set to username - email is still required, so check
		if ($this->login_column != 'email' and empty($user['email']))
		{
			throw new SentryUserException(
				__('sentry::sentry.column_email_and_password_empty', array('column' => $this->login_column_str))
			);
		}

		// check to see if login_column is already taken
		$user_exists = $this->user_exists($user[$this->login_column]);
		if ($user_exists)
		{
			// create new user object
			$temp = new static((int) $user_exists['user']['id']);

			// check if account is not activated
			if ($activation and $temp->get('activated') != 1)
			{
				// update and resend activation code
				$hash = Str::random(24);

				$update = array(
					'password' => $user['password'],
					'activation_hash' => $hash
				);

				if ($temp->update($update))
				{
					return array(
						'id'   => $temp->user['id'],
						'hash' => base64_encode($user[$temp->login_column]).'/'.$hash
					);
				}
				return false;
			}

			// if login_column is not set to email - also check to make sure email doesn't exist
			if ($this->login_column != 'email' and $this->user_exists($user['email'], 'email'))
			{
				throw new SentryUserException(__('sentry::sentry.email_already_in_use'));
			}
			throw new SentryUserException(
				__('sentry::sentry.column_already_exists', array('column' => $this->login_column_str))
			);
		}

		// set new user values
		$new_user = array(
			$this->login_column => $user[$this->login_column],
			'password' => $this->hash->create_password($user['password']),
			'created_at' => time(),
			'activated' => (bool) ($activation) ? false : true,
			'status' => 1,
		) + $user;

		// check for metadata
		if (array_key_exists('metadata', $new_user))
		{
			$metadata = $new_user['metadata'];
			unset($new_user['metadata']);
		}
		else
		{
			$metadata = array();
		}

		if (array_key_exists('permissions', $new_user))
		{
			$new_user['permissions'] = json_encode($new_user['permissions']);
		}

		// set activation hash if activation = true
		if ($activation)
		{
			$hash = Str::random(24);
			$new_user['activation_hash'] = $this->hash->create_password($hash);
		}

		// insert new user
		$insert_id= DB::connection($this->db_instance)
			->table($this->table)
			->insert_get_id($new_user);

		// insert into metadata
		$metadata = array(
			'user_id' => $insert_id
		) + $metadata;

		DB::connection($this->db_instance)
			->table($this->table_metadata)
			->insert($metadata);

		// return activation hash for emailing if activation = true
		if ($activation)
		{
			// return array of id and hash
			if ($insert_id)
			{
				return array(
					'id'   => (int) $insert_id,
					'hash' => base64_encode($user[$this->login_column]).'/'.$hash
				);
			}

			return false;
		}
		return ($insert_id) ? (int) $insert_id : false;
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
			throw new SentryUserException(__('sentry::sentry.no_user_selected'));
		}

		// init update array
		$update = array();

		// init user metatdata
		$update_metadata = null;

		if (array_key_exists($this->login_column, $fields) and
			strtolower($fields[$this->login_column]) != strtolower($this->user[$this->login_column]) and
			$this->user_exists($fields[$this->login_column]))
		{
			throw new SentryUserException(
				__('sentry::sentry.column_already_exists', array('column' => $this->login_column_str))
			);
		}
		elseif (array_key_exists($this->login_column, $fields) and
				$fields[$this->login_column] == '')
		{
			throw new SentryUserException(
				__('sentry::sentry.column_is_empty', array('column' => $this->login_column_str))
			);
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
				throw new SentryUserException(__('sentry::sentry.email_already_in_use'));
			}
			$update['email'] = $fields['email'];
			unset($fields['email']);
		}

		// if updating username
		if (array_key_exists('username', $fields) and
			$fields['username'] != $this->user['username'])
		{
			// make sure username does not already exist
			if ($this->user_exists($fields['username'], 'username'))
			{
				throw new SentryUserException(__('sentry::sentry.username_already_in_use'));
			}
			$update['username'] = $fields['username'];
			unset($fields['username']);
		}

		// if updating username
		if (array_key_exists('username', $fields) and
			$fields['username'] != $this->user['username'])
		{
			// make sure email does not already exist
			if ($this->user_exists($fields['username'], 'username'))
			{
				throw new SentryUserException(__('sentry::sentry.username_already_in_use'));
			}
			$update['username'] = $fields['username'];
			unset($fields['username']);
		}

		// update password
		if (array_key_exists('password', $fields))
		{
			if (empty($fields['password']))
			{
				throw new SentryUserException(__('sentry::sentry.password_empty'));
			}
			if ($hash_password)
			{
				$fields['password'] = $this->hash->create_password($fields['password']);
			}
			$update['password'] = $fields['password'];
			unset($fields['password']);
		}

		// update temp password
		if (array_key_exists('temp_password', $fields))
		{
			if ( ! empty($fields['temp_password']))
			{
				$fields['temp_password'] = $this->hash->create_password($fields['temp_password']);
			}
			$update['temp_password'] = $fields['temp_password'];
			unset($fields['temp_password']);
		}

		// update password reset hash
		if (array_key_exists('password_reset_hash', $fields))
		{
			if ( ! empty($fields['password_reset_hash']))
			{
				$fields['password_reset_hash'] = $this->hash->create_password($fields['password_reset_hash']);
			}
			$update['password_reset_hash'] = $fields['password_reset_hash'];
			unset($fields['password_reset_hash']);
		}

		// update remember me cookie hash
		if (array_key_exists('remember_me', $fields))
		{
			if ( ! empty($fields['remember_me']))
			{
				$fields['remember_me'] = $this->hash->create_password($fields['remember_me']);
			}
			$update['remember_me'] = $fields['remember_me'];
			unset($fields['remember_me']);
		}

		if (array_key_exists('activation_hash', $fields))
		{
			if ( ! empty($fields['activation_hash']))
			{
				$fields['activation_hash'] = $this->hash->create_password($fields['activation_hash']);
			}
			$update['activation_hash'] = $fields['activation_hash'];
			unset($fields['activation_hash']);
		}

		if (array_key_exists('last_login', $fields) and ! empty($fields['last_login']) and is_int($fields['last_login']))
		{
			$update['last_login'] = $fields['last_login'];
			unset($fields['last_login']);
		}

		if (array_key_exists('ip_address', $fields))
		{
			$update['ip_address'] = $fields['ip_address'];
			unset($fields['ip_address']);
		}

		if (array_key_exists('activated', $fields))
		{
			$update['activated'] = $fields['activated'];
			unset($fields['activated']);
		}

		if (array_key_exists('status', $fields))
		{
			$update['status'] = $fields['status'];
			unset($fields['status']);
		}

		if (array_key_exists('permissions', $fields))
		{
			$permissions = $this->process_permissions($fields['permissions']);
			$update['permissions'] = json_encode($permissions);
			unset($fields['permissions']);
		}

		if (empty($update) and empty($fields['metadata']))
		{
			return true;
		}

		// add update time
		$update['updated_at'] = time();

		// update user table
		if ($update)
		{
			$update_user = DB::connection($this->db_instance)
				->table($this->table)
				->join($this->table_metadata,
							$this->table_metadata.'.user_id', '=', $this->table.'.id')
				->where('id', '=', $this->user['id'])
				->update($update);
		}

		// update metadata table
		if ( ! empty($fields['metadata']))
		{
			$update_metadata = DB::connection($this->db_instance)
				->table($this->table_metadata)
				->where('user_id', '=', $this->user['id'])
				->update($fields['metadata']);
		}
		else
		{
			$fields['metadata'] = array();
		}

		if ($update_user or $update_metadata)
		{
			$update['metadata'] = $fields['metadata'] + $this->user['metadata'];

			// lets remove passwords from global user array
			$update = $this->extract_passwords($update);

			// change user values in object
			$this->user = $update['user'] + $this->user;
			$this->passwords = $update['passwords'] + $this->passwords;

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
			throw new SentryUserException(__('sentry::sentry.no_user_selected_to_delete'));
		}

		try
		{
			DB::connection($this->db_instance)->pdo->beginTransaction();

			// delete users groups
			$delete_user_groups = DB::connection($this->db_instance)
				->table($this->table_usergroups)
				->where('user_id', '=', $this->user['id'])
				->delete();

			// delete users metadata
			$delete_user_metadata = DB::connection($this->db_instance)
				->table($this->table_metadata)
				->where('user_id', '=', $this->user['id'])
				->delete();

			// delete user from database
			$delete_user = DB::connection($this->db_instance)
				->table($this->table)
				->where('id', '=', $this->user['id'])
				->delete();

			DB::connection($this->db_instance)->pdo->commit();
		}
		catch(Database_Exception $e) {

			DB::connection($this->db_instance)->pdo->rollBack();
			return false;
		}

		// update user to null
		$this->user = array();
		$this->passwords = array();

		return true;

	}

	/**
	 * Enable a User
	 *
	 * @return  bool
	 * @throws  SentryUserException
	 */
	public function enable()
	{
		if ($this->user['status'] == 1)
		{
			throw new SentryUserException(__('sentry::sentry.user_already_enabled'));
		}
		return $this->update(array('status' => 1));
	}

	/**
	 * Disable a User
	 *
	 * @return  bool
	 * @throws  SentryUserException
	 */
	public function disable()
	{
		if ($this->user['status'] == 0)
		{
			throw new SentryUserException(__('sentry::sentry.user_already_disabled'));
		}
		return $this->update(array('status' => 0));
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
			throw new SentryUserException(__('sentry::sentry.no_user_selected_to_get'));
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
				// see if field is a password field
				// see if field is a password field
				if (in_array($key, $this->password_fields))
				{
					$val = array_get($this->passwords, $key, '__MISSING_KEY__');
				}
				else
				{
					 // check to see if field exists in user
					$val = array_get($this->user, $key, '__MISSING_KEY__');
				}

				if ($val !== '__MISSING_KEY__')
				{
					$values[$key] = $val;
				}
				else
				{
					throw new SentryUserException(
						__('sentry::sentry.not_found_in_user_object', array('field' => $key))
					);
				}
			}

			return $values;
		}
		// if single field was passed - return its value
		else
		{
			// see if field is a password field
			if (in_array($field, $this->password_fields))
			{
				$val = array_get($this->passwords, $field, '__MISSING_KEY__');
			}
			else
			{
				// check to see if field exists in user
				$val = array_get($this->user, $field, '__MISSING_KEY__');
			}

			// if val is not missing, return it
			if ($val !== '__MISSING_KEY__')
			{
				return $val;
			}

			throw new SentryUserException(__('sentry::sentry.not_found_in_user_object', array('field' => $field)));
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
			throw new SentryUserException(__('sentry::sentry.invalid_old_password'));
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
	 * @throws  SentryUserException
	 */
	public function add_to_group($id)
	{
		if ($this->in_group($id))
		{
			throw new SentryUserException(__('sentry::sentry.user_already_in_group', array('group' => $id)));
		}

		$field = 'name';
		if (is_numeric($id))
		{
			$field = 'id';
		}

		try
		{
			$group = new Sentry_Group($id);
		}
		catch (SentryGroupNotFoundException $e)
		{
			throw new SentryUserException($e->getMessage());
		}

		$insert_id = DB::connection($this->db_instance)
			->table($this->table_usergroups)
			->insert_get_id(array(
				'user_id' => $this->user['id'],
				'group_id' => $group->get('id'),
			));

		$this->groups[] = array(
			'id'       => $group->get('id'),
			'name'     => $group->get('name'),
		);

		return true;
	}

	/**
	 * Removes this user from the group.
	 *
	 * @param   string|int  Group ID or group name
	 * @return  bool
	 * @throws  SentryUserException
	 */
	public function remove_from_group($id)
	{
		if ( ! $this->in_group($id))
		{
			throw new SentryUserException(__('sentry::sentry.user_not_in_group', array('group' => $id)));
		}

		$field = 'name';
		if (is_numeric($id))
		{
			$field = 'id';
		}

		try
		{
			$group = new Sentry_Group($id);
		}
		catch (SentryGroupNotFoundException $e)
		{
			throw new SentryUserException($e->getMessage());
		}

		$delete = DB::connection($this->db_instance)
			->table($this->table_usergroups)
			->where('user_id', '=', $this->user['id'])
			->where('group_id', '=', $group->get('id'))
			->delete();

		// remove from array
		$field = 'name';
		if (is_numeric($id))
		{
			$field = 'id';
		}

		foreach ($this->groups as $key => $group)
		{
			if ($group[$field] == $id)
			{
				unset($this->groups[$key]);
			}
		}

		return (bool) $delete;
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
		$result = DB::connection($this->db_instance)
			->table($this->table)
			->where($field, '=', $login)
			->first();

		if ($result !== null)
		{
			$result = get_object_vars($result);

			$metadata = DB::connection($this->db_instance)
					->table($this->table_metadata)
					->where('user_id', '=', $result['id'])
					->first();

			$result['metadata'] = ($metadata != null) ? get_object_vars($metadata) : array();

			// lets set and remove password fields
			$result = $this->extract_passwords($result);

			return $result;
		}

		return false;
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
		if ($this->hash->check_password($password, $this->passwords[$field]))
		{
			return true;
		}

		if (Config::get('sentry::sentry.hash.convert.enabled') === true)
		{
			$strategy = Config::get('sentry::sentry.hash.convert.from');
			$options = Config::get('sentry::sentry.hash.strategies.'.$strategy);
			$hash = Sentry_Hash_Driver::forge($strategy, $options);

			if ($hash->check_password($password, $this->passwords[$field]))
			{
				$this->update(array(
					'password' => $password
				));

				return true;
			}
		}

		return false;
	}

	/**
	 * Return all users
	 *
	 * @return  array
	 */
	public function all()
	{
		$users = DB::connection($this->db_instance)
			->table($this->table)
			->get();

		foreach ($users as &$user)
		{
			$user = get_object_vars($user);
		}

		return $users;
	}

	/**
	 * Return user's custom permissions json
	 *
	 * @return  array|json
	 */
	public function permissions()
	{
		return $this->get('permissions');
	}

	/**
	 * Return user's merged permissions
	 *
	 * @return  array
	 */
	public function merged_permissions()
	{
		return $this->permissions;
	}

	/**
	 * Add/Update group permission rules.
	 *
	 * Usage:
	 *
	 * $permissions_to_add = array(
	 *      'blog_admin_create' => 1, // setting to 1 will add it to the group
	 *      'blog_admin_delete' => 0, // setting to zero will remove it from the group if it is in there.
	 * );
	 *
	 * Sentry::user()->update_permissions($permissions_to_add);
	 *
	 * @param array|string $rules
	 * @return bool
	 * @throws SentryPermissionsException
	 */
	public function update_permissions($rules = array())
	{
		// get and reformat permissions
		$current_permissions = $this->process_permissions($rules);

		if (empty($current_permissions))
		{
			return $this->update(array('permissions' => ''));
		}
		else
		{
			// let's update the permissions column.
			return $this->update(array('permissions' => json_encode($current_permissions)));
		}
	}

	protected function process_permissions($rules = array())
	{
		if (empty($rules) or ! is_array($rules))
		{
			throw new SentryPermissionsException(__('sentry::sentry.no_rules_added'));
		}

		// loop through the rules and make sure all values are a 1 or 0
		foreach ($rules as $rule => $value)
		{
			if ( ! empty($value) and $value !== 0 and $value !== 1)
			{
				throw new SentryUserPermissionsException('A permission value must be empty or an integer of 1 or 0. Value passed: '.$value.' ('.gettype($value).')');
			}
		}

		$current_permissions = json_decode($this->user['permissions'], true);
		$current_permissions = ( is_array($current_permissions) ) ? $current_permissions : array();

		foreach ($rules as $key => $val)
		{
			if (in_array($key, $this->rules) or $key === Config::get('sentry::sentry.permissions.superuser'))
			{
				if ($val === 1 or $val === 0)
				{
					$current_permissions[$key] = $val;
				}
				else
				{
					unset($current_permissions[$key]);
				}
			}
			else
			{
				throw new SentryPermissionsException(__('sentry::sentry.rule_not_found', array('rule' => $key)));
			}
		}

		return $current_permissions;
	}


	/**
	 * Check to see if the user has access to a resource
	 *
	 * The user can specify a specific resource. If no resource is provided,
	 * then Sentry will generate the resource automatically. If the resource
	 * is found in the configured rules provided in the config file then the
	 * user's current merged permissions array will be checked.
	 *
	 * @param   null $resource
	 * @return  bool
	 */
	public function has_access($resource = null)
	{
		/**
		 * If we have a super user (this is the global administrator,
		 * than just return true and skip checks
		 */
		if (in_array(Config::get('sentry::sentry.permissions.superuser'), $this->permissions))
		{
			return true;
		}

		/**
		 * Get the current page in our rule format
		 * We'll use this if there is no $resource set and to check our array against.
		 */
		$bundle     = Request::route()->bundle;
		$controller = Request::route()->controller;
		$action     = Request::route()->controller_action;

		// build this resource string
		$current_resource = $bundle;
		if ($controller)
		{
			$current_resource .= '::'.$controller;

			if ($action)
			{
				$current_resource .= '@'.$action;
			}
		}

		// lets make the resource an array by default
		$resource = ($resource) ?: $current_resource;

		if ( ! is_array($resource))
		{
			$resource = array($resource);
		}

		// Loop through the resources and check if it exists in the rules/permissions
		foreach ($resource as $rule)
		{
			// if it is in the config rules & not in the array rules, than we don't have access.
			if (in_array($rule, $this->rules) and ! in_array($rule, $this->permissions))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Extracts passwords from the user array
	 *
	 * @param   array  user array
	 * @return  array  array of the user and extracted passwords
	 */
	protected function extract_passwords($user)
	{
		$passwords = array();
		foreach ($user as $field => $value)
		{
			if (in_array($field, $this->password_fields))
			{
				$passwords[$field] = $value;
				unset($user[$field]);
			}
		}

		return array(
			'user'      => $user,
			'passwords' => $passwords
		);
	}

	protected function fetch_permissions()
	{
		// set permissions arrray
		$permissions = array();

		// let's get the group permissions first.
		foreach ($this->groups as $group)
		{
			if ( ! empty($group['permissions']))
			{
				// grab and decode the group's permissions
				$group_permissions = json_decode($group['permissions'], true);

				foreach ($group_permissions as $key => $val)
				{
					// add the key to the permissions array if it doesn't exist already
					if ( ! empty($key) and $val === 1)
					{
						if ( ! in_array($key, $permissions))
						{
							$permissions[] = $key;
						}
					}
					// remove the key from the array
					else
					{
						$permissions = array_diff($permissions, array($key));
					}
				}
			}
		}

		// now let's merge the user's permissions
		if ( ! empty($this->user['permissions']))
		{
			// grab and decode the user's permissions
			$user_permissions = json_decode($this->user['permissions'], true);

			foreach ($user_permissions as $key => $val)
			{
				// add to array
				if ($val === 1)
				{
					if ( ! in_array($key, $permissions))
					{
						$permissions[] = $key;
					}
				}
				// remove from array
				else
				{
					$permissions = array_diff($permissions, array($key));
				}
			}
		}

		return array_values($permissions);
	}


	/**
	 * Implementation of the Iterator interface
	 */

	protected $_iterable = array();

	public function rewind()
	{
		$this->_iterable = $this->user;
		reset($this->_iterable);
	}

	public function current()
	{
		return current($this->_iterable);
	}

	public function key()
	{
		return key($this->_iterable);
	}

	public function next()
	{
		return next($this->_iterable);
	}

	public function valid()
	{
		return key($this->_iterable) !== null;
	}

	/**
	 * Sets the value of the given offset (class property).
	 *
	 * @param   string  $offset  class property
	 * @param   string  $value   value
	 * @return  void
	 */
	public function offsetSet($offset, $value)
	{
		$this->{$offset} = $value;
	}

	/**
	 * Checks if the given offset (class property) exists.
	 *
	 * @param   string  $offset  class property
	 * @return  bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->{$offset});
	}

	/**
	 * Unsets the given offset (class property).
	 *
	 * @param   string  $offset  class property
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		unset($this->{$offset});
	}

	/**
	 * Gets the value of the given offset (class property).
	 *
	 * @param   string  $offset  class property
	 * @return  mixed
	 */
	public function offsetGet($offset)
	{
		if (isset($this->{$offset}))
		{
			return $this->{$offset};
		}

		throw new \OutOfBoundsException('Property "'.$offset.'" not found for '.get_called_class().'.');
	}

}
