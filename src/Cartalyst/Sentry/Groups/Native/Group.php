<?php namespace Cartalyst\Sentry\Groups\Native;
/**
 * Part of the Sentry Package.
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
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Groups\GroupInterface;

class Group implements GroupInterface {

	/**
	 * Model attributes
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * Instance of an already set up PDO connection
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * Index column in table
	 *
	 * @var string
	 */
	protected $tableIndex = 'id';

	/**
	 * Table's name where our groups are stored
	 *
	 * @var string
	 */
	protected $table = 'groups';

	public function __construct(PDO $pdo, array $attributes, $table = null)
	{

		// Assign variables
		$this->pdo = $pdo;

		$this->fill($attributes);

		if (isset($table))
			$this->table = $table;

	}

	/**
	 * Returns the group's ID.
	 *
	 * @return mixed
	 */
	public function getGroupId()
	{

		if (isset($this->attributes['id'])) {
			return $this->attributes['id'];
		} else {
			throw new InvalidArgumentException("Requested id isn't set yet, Group doesn't exist?");
		}

	}

	/**
	 * Returns the group's name.
	 *
	 * @return string
	 */
	public function getGroupName()
	{
		return $this->attributes['name'];
	}

	/**
	 * Returns permissions for the group.
	 *
	 * @return array
	 */
	public function getGroupPermissions()
	{
		return $this->attributes['permissions'];
	}

	/**
	 * Saves the group.
	 *
	 * @return bool
	 */
	public function save()
	{

		// current date_time, we shouldn't use e.g. NOW(), since it's only mysql...
		$datetime = date('Y-m-d H:i:s');

		// base bindings, data which will always be used in any query
		$baseBindings = array(
			$this->attributes['name'],
			$this->attributes['permissions'],
			$this->attributes['updated_at']
		);

		/**
		 * Persisting models in this case involve two simplified states: new, and managed.
		 *
		 * When a model is new: it won't have an ID set, since it's an auto increment
		 * value, and we can't have it until it has been inserted.
		 *
		 * When it's managed: it'll have all of the attributes which can be stored in the
		 * database including the ID.
		 */
		if (!isset($this->attributes['id'])) {
			$this->createNew($baseBindings, $datetime);
		} else {
			$this->updateExisting($baseBindings);
		}

	}

	/**
	 * Persistence of a new entity
	 *
	 * @param array $baseBindings
	 * @param string $datetime
	 * @return bool
	 */
	protected function createNew(array $baseBindings, $datetime = null)
	{
		$query = "
			INSERT INTO " . $this->table . " (name,permissions,created_at,updated_at)
			VALUES (:name, :permissions, :created_at, :updated_at)
		";

		$specialBindings = array(
			':created_at'	=> $datetime
		);

		$bindings = array_merge($baseBindings, $specialBindings);

		try {

			$this->pdo->beginTransaction();
			$ps = $this->pdo->prepare($query);
			$ps->execute($bindings);
			$this->pdo->commit();

			// this is one of the reasons why we had to separate the update / insert proc.
			$this->attributes['id'] = $ps->lastInsertId($this->tableIndex);

			return true;

		} catch (\PDOException $pdoe) {
			
			// forward the exception...
			throw new \Exception($pdoe->getMessage());
			
			return false;
		}

	}

	/**
	 * Persistence of an already existing entity
	 *
	 * @param array $baseBindings
	 * @return bool
	 */
	protected function updateExisting(array $baseBindings)
	{
		$query = "
			UPDATE " . $this->table . "
			SET name = :name, permissions = :permissions, updated_at = :updated_at
			WHERE id = :id
		";

		$specialBindings = array(
			':id'	=> $this->attributes['id']
		);

		$bindings = array_merge($baseBindings, $specialBindings);

		try {

			$this->pdo->beginTransaction();
			$ps = $this->pdo->prepare($query);
			$ps->execute($bindings);
			$this->pdo->commit();

			return true;

		} catch (\PDOException $pdoe) {
			
			// forward the exception...
			throw new \Exception($pdoe->getMessage());
			
			return false;
		}

	}

	/**
	 * Utility method, for error handled assignment
	 *
	 * @param array $attributes
	 * @throws InvalidArgumentException
	 * @return void
	 */
	protected function fill(array $attributes)
	{
		if (!is_empty($attributes)) {
			$this->attributes = $attributes;
		} else {
			throw new \InvalidArgumentException("Expected an array of attributes!");
		}
	}

	/**
	 * Delete the current entity
	 * 
	 * @return bool
	 */
	public function delete()
	{
		
		$query = "DELETE FROM " . $this->table . " WHERE id = :id";
		$bindings = array(':id'	=> $this->attributes['id']);

		try {
			$this->pdo->beginTransaction();
			$ps = $this->pdo->prepare($query);
			$ps->execute($bindings);
			$this->pdo->commit();

			return true;

		} catch (\PDOException $pdoe) {
			return false;
		}
		
	}

}