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
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\ProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * Instance of a PDO connection
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * Table's name where our groups are stored
	 *
	 * @var string
	 */
	protected $table = 'groups';

	/**
	 * Database configurations
	 *
	 * @var array
	 */
	protected $dbConfig;

	public function __construct(array $dbConfig, $table = null)
	{

		// Assigning configurations
		if (!empty($dbConfig))
			$this->dbConfig = $dbConfig;

		if (isset($table))
			$this->table = $table;

		// The default configurations
		$sentryDefaults = array(
			'engine' 	=> 'mysql',
			'host'		=> 'localhost',
			'database' 	=> 'nonexistentdb',
			'user' 		=> 'root',
			'password'	=> '',
		);

		// Merge the given ones
		$this->dbConfig = array_merge($sentryDefaults, $this->dbConfig);

		// Instantiate PDO with the given settings
		$this->pdo = new PDO(
			$this->config['engine'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['database'],
			$this->config['user'],
			$this->config['password']
		);

		// Set error mode
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Set character set as well
		$this->pdo->exec("SET CHARACTER SET utf8");
	}

	/**
	* Find group by ID.
	*
	* @param int $id
	* @return Cartalyst\Sentry\GroupInterface $group
	* @throws Cartalyst\Sentry\GroupNotFoundException
	*/
	public function findById($id)
	{

		// Preparations
		$query = 'SELECT * from ? where `id` = ?';
		$bindings = array($this->table, $id);

		// Execution
		$ps = $this->pdo->prepare($query);
		$ps->execute($bindings);

		$attributes = $ps->fetch(PDO::FETCH_ASSOC);

		// Returning with something
		if (!empty($attributes)) {
			$newModel = new \Cartalyst\Sentry\Groups\Native\Group($this->pdo, $attributes);

			return $newModel;
		} else {
			throw new GroupNotFoundException("Couldn't find group with id [$id]!");
		}

	}

	/**
	* Find group by name.
	*
	* @param string $name
	* @return Cartalyst\Sentry\GroupInterface $newGroup
	* @throws Cartalyst\Sentry\GroupNotFoundException
	*/
	public function findByName($name)
	{

		// Preparations
		$query = 'SELECT * from ? where `name` = ?';
		$bindings = array($this->table, $name);

		// Execution
		$ps = $this->pdo->prepare($query);
		$ps->execute($bindings);

		$attributes = $ps->fetch(PDO::FETCH_ASSOC);

		// Returning with something
		if (!empty($attributes)) {
			$newGroup = new \Cartalyst\Sentry\Groups\Native\Group($this->pdo, $attributes);

			return $newGroup;
		} else {
			throw new GroupNotFoundException("Couldn't find group with name [$name]!");
		}

	}

	/**
	* Creates a group.
	*
	* @param array $attributes
	* @throws Exception
	* @return Cartalyst\Sentry\Groups\GroupInterface
	*/
	public function create(array $attributes)
	{

		$newGroup = new \Cartalyst\Sentry\Groups\Native\Group($this->pdo, $attributes);

		if (!$newGroup->save()) {
			throw new Exception("Error while creating new group!");
		} else {
			return $newGroup;
		}

	}

}