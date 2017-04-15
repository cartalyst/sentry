<?php namespace Cartalyst\Sentry\Sessions\Native;
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

class DatabaseSession implements SessionInterface {

	/**
	 * The key used in the Session.
	 *
	 * @var string
	 */
	protected $key = 'cartalyst_sentry';

	/**
	 * Session store object.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $config;
	
	protected $table = 'sessions';
	
	protected $contents = array();

	/**
	 * Creates a new Illuminate based Session driver
	 * for Sentry.
	 *
	 * @param  Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(array $config, $table = null, $key = null)
	{
		// Assigning configurations
		if (!empty($config))
			$this->config = $config;
		
		if (isset($table))
			$this->table = $table;
		
		if (isset($key))
			$this->key = $key;

		// The default configurations
		$sentryDefaults = array(
			'engine' 	=> 'mysql',
			'host'		=> 'localhost',
			'database' 	=> 'nonexistentdb',
			'user' 		=> 'root',
			'password'	=> '',
		);

		// Merge the given ones
		$this->config = array_merge($sentryDefaults, $this->config);

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
		
		// init session
		session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
		
	}

	/**
	 * Returns the session key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Put a key / value pair in the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function put($key, $value)
	{
		$this->session->put($key, $value);
	}

	/**
	 * Get the requested item from the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		return $this->session->get($key, $default);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		$this->session->forget($key);
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->forget($this->key);
	}
	
	public function read()
	{
		$query = "SELECT * FROM " . $this->table . " WHERE id = :id";
		$bindings = array(':id' => $this->key);

		try {

			$this->pdo->beginTransaction();
			$ps = $this->pdo->prepare($query);
			$ps->execute($bindings);
			$this->pdo->commit();

			$this->contents = unserialize($this->pdo->fetch(PDO::FETCH_ASSOC));
			return $this->contents;

		} catch (\PDOException $pdoe) {
			
			// forward the exception...
			throw new \Exception($pdoe->getMessage());
			
			return false;
		}
	}
	
	public function save()
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

}