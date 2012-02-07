<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

use ArrayAccess;
use Config;
use DB;
use FuelException;
use Iterator;

class SentryGroupException extends \FuelException {}
class SentryGroupNotFoundException extends \SentryGroupException {}

/**
 * Handles all of the Sentry group logic.
 *
 * @author Dan Horrigan
 */
class Sentry_Group implements Iterator, ArrayAccess
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
	 * Gets the table names
	 */
	public static function _init()
	{
		static::$table = strtolower(Config::get('sentry.table.groups'));
		static::$join_table = strtolower(Config::get('sentry.table.users_groups'));
		$_db_instance = trim(Config::get('sentry.db_instance'));

		// db_instance check
		if ( ! empty($_db_instance) )
		{
			static::$db_instance = $_db_instance;
		}
	}

	/**
	 * Gets all the group info.
	 *
	 * @param   string|int  Group id or name
	 * @return  void
	 */
	public function __construct($id = null)
	{
		if ($id === null)
		{
			return;
		}

		if (is_numeric($id))
		{
			if ($id <= 0)
			{
				throw new \SentryGroupException(__('sentry.invalid_group_id'));
			}
			$field = 'id';
		}
		else
		{
			$field = 'name';
		}

		$group = DB::select()
		          ->from(static::$table)
		          ->where($field, $id)
		          ->execute(static::$db_instance);

		// if there was a result - update user
		if (count($group))
		{
			$this->group = $group->current();
		}
		// group doesn't exist
		else
		{
			throw new \SentryGroupNotFoundException(__('sentry.group_not_found', array('group' => $id)));
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
			throw new \SentryGroupException(__('sentry.group_name_empty'));
		}

		if (Sentry::group_exists($group['name']))
		{
			throw new \SentryGroupException(__('sentry.group_already_exists', array('group' => $group['name'])));
		}

		if ( ! array_key_exists('level', $group))
		{
			throw new \SentryGroupException(__('sentry.group_level_empty'));
		}

		if ( ! array_key_exists('is_admin', $group))
		{
			$group['is_admin'] = 0;
		}

		if ( ! array_key_exists('parent', $group))
		{
			$group['parent'] = 0;
		}

		list($insert_id, $rows_affected) = DB::insert(static::$table)->set($group)->execute(static::$db_instance);

		return ($rows_affected > 0) ? $insert_id : false;
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
			throw new \SentryGroupException(__('sentry.no_group_selected'));
		}

		// init the update array
		$update = array();

		// update name
		if (array_key_exists('name', $fields) and $fields['name'] != $this->group['name'])
		{
			// make sure name does not already exist
			if (Sentry::group_exists($fields['name']))
			{
				throw new \SentryGroupException(__('sentry.group_already_exists', array('group' => $fields['name'])));
			}
			$update['name'] = $fields['name'];
			unset($fields['name']);
		}

		// update level
		if (array_key_exists('level', $fields))
		{
			$update['level'] = $fields['level'];
		}

		// update is_admin
		if (array_key_exists('is_admin', $fields))
		{
			$update['is_admin'] = $fields['is_admin'];
		}

		if (empty($update))
		{
			return true;
		}

		$update_group = DB::update(static::$table)
			->set($update)
			->where('id', $this->group['id'])
			->execute(static::$db_instance);

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
			throw new \SentryGroupException(__('sentry.no_group_selected'));
		}

		DB::start_transaction();

		try
		{
			// delete users groups
			$delete_user_groups = DB::delete(static::$join_table)
				->where('group_id', $this->group['id'])
				->execute(static::$db_instance);

			// delete GROUP
			$delete_user = DB::delete(static::$table)
				->where('id', $this->group['id'])
				->execute(static::$db_instance);
		}
		catch(\Database_Exception $e) {
			DB::rollback_transaction();
			return false;
		}

		DB::commit_transaction();

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
			throw new \SentryGroupException(__('sentry.no_group_selected'));
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
					throw new \SentryGroupException(
						__('sentry.not_found_in_group_object', array('field' => $key))
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

			throw new \SentryGroupException(
				__('sentry.not_found_in_group_object', array('field' => $field))
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
		$users_table = Config::get('sentry.table.users');
		$groups_table = Config::get('sentry.table.groups');

		$users = DB::select($users_table.'.*')
			->from($users_table)
			->where(static::$join_table.'.group_id', '=', $this->group['id'])
			->join(static::$join_table)
			->on(static::$join_table.'.user_id', '=', $users_table.'.id')
			->execute(static::$db_instance)->as_array();

		if (count($users) == 0)
		{
			return array();
		}

		// Unset password stuff
		foreach ($users as & $user)
		{
			unset($user['password']);
			unset($user['password_reset_hash']);
			unset($user['temp_password']);
			unset($user['remember_me']);
		}

		return $users;
	}

	/**
	 * Retruns all groups
	 *
	 * @return  array
	 */
	public function all()
	{
		return DB::select()->from(static::$table)->execute(static::$db_instance)->as_array();
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
