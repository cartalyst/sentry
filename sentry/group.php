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

class SentryGroupException extends SentryException {}
class SentryGroupNotFoundException extends SentryGroupException {}
class SentryGroupPermissionsException extends SentryGroupException {}

/**
 * Handles all of the Sentry group logic.
 */
class Sentry_Group implements \Iterator, \ArrayAccess
{

	/**
	 * @var  string  Database instance
	 */
	protected static $db_instance = null;

	/**
	 * @var  string  Group table
	 */
	protected static $table = '';

	/**
	 * @var  string  User/group join table
	 */
	protected static $join_table = '';

	/**
	 * @var  array  Group array
	 */
	protected $group = array();

	/**
	 * @var  array  Contains the rules from the sentry config
	 */
	protected static $rules = array();

	/**
	 * Gets all the group info.
	 *
	 * @param   string|int  Group id or name
	 * @return  void
	 */
	public function __construct($id = null, $check_exists = false)
	{
		static::$table = strtolower(Config::get('sentry::sentry.table.groups'));
		static::$join_table = strtolower(Config::get('sentry::sentry.table.users_groups'));
		$db_instance = trim(Config::get('sentry::sentry.db_instance'));

		// db_instance check
		if ( ! empty($db_instance) )
		{
			static::$db_instance = $db_instance;
		}

		if ($id === null)
		{
			return;
		}

		if (is_numeric($id))
		{
			if ($id <= 0)
			{
				throw new SentryGroupException(__('sentry::sentry.invalid_group_id'));
			}
			$field = 'id';
		}
		else
		{
			$field = 'name';
		}

		$group = DB::connection(static::$db_instance)
		          ->table(static::$table)
		          ->where($field, '=', $id)
		          ->first();

		// if there was a result - update user
		if ($group !== null)
		{
			if ($check_exists)
			{
				return true;
			}

			$this->group = get_object_vars($group);
		}
		// group doesn't exist
		else
		{
			throw new SentryGroupNotFoundException(__('sentry::sentry.group_not_found', array('group' => $id)));
		}
	}

	/**
	 * Creates the given group.
	 *
	 * @param   array  Group info
	 * @return  int|bool
	 */
	public function create($group)
	{
		if ( ! array_key_exists('name', $group))
		{
			throw new SentryGroupException(__('sentry::sentry.group_name_empty'));
		}

		if (static::group_exists($group['name']))
		{
			throw new SentryGroupException(__('sentry::sentry.group_already_exists', array('group' => $group['name'])));
		}

		$insert_id = DB::connection(static::$db_instance)
			->table(static::$table)
			->insert_get_id($group);

		return ($insert_id) ? (int) $insert_id : false;
	}

	/**
	 * Update the given group
	 *
	 * @param   array  fields to be updated
	 * @return  bool
	 * @throws  SentryGroupException
	 */
	public function update(array $fields)
	{
		// make sure a group id is set
		if (empty($this->group['id']))
		{
			throw new SentryGroupException(__('sentry::sentry.no_group_selected'));
		}

		// init the update array
		$update = array();

		// update name
		if (array_key_exists('name', $fields) and $fields['name'] != $this->group['name'])
		{
			// make sure name does not already exist
			if (static::group_exists($fields['name']))
			{
				throw new SentryGroupException(__('sentry::sentry.group_already_exists', array('group' => $fields['name'])));
			}
			$update['name'] = $fields['name'];
			unset($fields['name']);
		}

		if (array_key_exists('permissions', $fields))
		{
			$permissions = $this->process_permissions($fields['permissions']);
			$update['permissions'] = json_encode($permissions);
			unset($fields['permissions']);
		}

		if (empty($update))
		{
			return true;
		}

		$update_group = DB::connection(static::$db_instance)
			->table(static::$table)
			->where('id', '=', $this->group['id'])
			->update($update);

		return ($update_group) ? true : false;

	}

	/**
	 * Delete's the current group.
	 *
	 * @return  bool
	 * @throws  SentryGroupException
	 */
	public function delete()
	{
		// make sure a user id is set
		if (empty($this->group['id']))
		{
			throw new SentryGroupException(__('sentry::sentry.no_group_selected'));
		}

		try
		{
			DB::connection(static::$db_instance)->pdo->beginTransaction();

			// delete users groups
			$delete_user_groups = DB::connection(static::$db_instance)
				->table(static::$join_table)
				->where('group_id', '=', $this->group['id'])
				->delete();

			// delete GROUP
			$delete_user = DB::connection(static::$db_instance)
				->table(static::$table)
				->where('id', '=', $this->group['id'])
				->delete();

			DB::connection(static::$db_instance)->pdo->commit();
		}
		catch(\Database_Exception $e) {

			DB::connection(static::$db_instance)->pdo->rollBack();
			return false;
		}

		// update user to null
		$this->group = array();
		return true;

	}

	/**
	 * Checks if the Field is set or not.
	 *
	 * @param   string  Field name
	 * @return  bool
	 */
	public function __isset($field)
	{
		return array_key_exists($field, $this->group);
	}

	/**
	 * Gets a field value of the group
	 *
	 * @param   string  Field name
	 * @return  mixed
	 * @throws  SentryGroupException
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
	 * @throws  SentryGroupException
	 */
	public function get($field = null)
	{
		// make sure a group id is set
		if (empty($this->group['id']))
		{
			throw new SentryGroupException(__('sentry::sentry.no_group_selected'));
		}

		// if no fields were passed - return entire user
		if ($field === null)
		{
			return $this->group;
		}
		// if field is an array - return requested fields
		else if (is_array($field))
		{
			$values = array();

			// loop through requested fields
			foreach ($field as $key)
			{
				// check to see if field exists in group
				if (array_key_exists($key, $this->group))
				{
					$values[$key] = $this->group[$key];
				}
				else
				{
					throw new SentryGroupException(
						__('sentry::sentry.not_found_in_group_object', array('field' => $key))
					);
				}
			}

			return $values;
		}
		// if single field was passed - return its value
		else
		{
			// check to see if field exists in group
			if (array_key_exists($field, $this->group))
			{
				return $this->group[$field];
			}

			throw new SentryGroupException(
				__('sentry::sentry.not_found_in_group_object', array('field' => $field))
			);
		}
	}

	/**
	 * Gets all the users for this group.
	 *
	 * @return  array
	 */
	public function users()
	{
		$users_table = Config::get('sentry::sentry.table.users');
		$groups_table = Config::get('sentry::sentry.table.groups');

		$users = DB::connection(static::$db_instance)
			->table($users_table)
			->where(static::$join_table.'.group_id', '=', $this->group['id'])
			->join(static::$join_table,
						static::$join_table.'.user_id', '=', $users_table.'.id')
			->get($users_table.'.*');

		if (count($users) == 0)
		{
			return array();
		}

		// Unset password stuff
		foreach ($users as &$user)
		{
			$user = get_object_vars($user);
			unset($user['password']);
			unset($user['password_reset_hash']);
			unset($user['activation_hash']);
			unset($user['temp_password']);
			unset($user['remember_me']);
		}

		return $users;
	}

	/**
	 * Returns all groups
	 *
	 * @return  array
	 */
	public function all()
	{
		$groups = DB::connection(static::$db_instance)
			->table(static::$table)
			->get();

		foreach ($groups as &$group)
		{
			$group = get_object_vars($group);
		}

		return $groups;
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
	 * Sentry::group('groupname/id')->update_permissions($permissions_to_add);
	 *
	 * @param  array $rules
	 * @return bool
	 * @throws SentryGroupPermissionsException
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
			throw new SentryGroupPermissionsException(__('sentry::sentry.no_rules_added'));
		}

		// loop through the rules and make sure all values are a 1 or 0
		foreach ($rules as $rule => $value)
		{
			if ( ! empty($value) and $value !== 1)
			{
				throw new SentryGroupPermissionsException('A permission value must be empty or an integer of 1. Value passed: '.$value.' ('.gettype($value).')');
			}
		}

		// grab the current group permissions and decode
		$current_permissions = json_decode($this->get('permissions'), true);
		$current_permissions = (is_array($current_permissions)) ? $current_permissions : array();

		// get sentry rules
		$all_rules = Sentry_Rules::fetch_rules();

		// Let's go through each of the $rules
		foreach ($rules as $key => $val)
		{
			// Check to make sure the rule is in the config
			if (in_array($key, $all_rules) or $key === Config::get('sentry::sentry.permissions.superuser'))
			{
				if ($val === 1)
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
				throw new SentryGroupPermissionsException(__('sentry::sentry.rule_not_found', array('rule' => $key)));
			}
		}

		return $current_permissions;
	}

	/**
	 * get the permissions for a single group
	 *
	 * @return  mixed|json
	 */
	public function permissions()
	{
		return $this->get('permissions');
	}

	/**
	 * Checks if the group exists
	 *
	 * @param   string|int  Group name|Group id
	 * @return  bool
	 */
	protected function group_exists($group)
	{
		if ( is_int( $group ) )
		{
			$group_exists = DB::connection(static::$db_instance)
				->table(static::$table)
				->find($group);
		}
		else
		{
			$group_exists = DB::connection(static::$db_instance)
				->table(static::$table)
				->where('name', '=', $group)
				->first();
		}

		return (bool) count($group_exists);
	}


	/**
	 * Implementation of the Iterator interface
	 */

	protected $_iterable = array();

	public function rewind()
	{
		$this->_iterable = $this->group;
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
