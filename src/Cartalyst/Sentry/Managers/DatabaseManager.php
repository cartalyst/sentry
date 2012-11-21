<?php namespace Cartalyst\Sentry\Managers;

use Illuminate\Support\Manager;
use Illuminate\Database\Connection;
use Illuminate\Events\Dispatcher as EventsDispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseManager implements ConnectionResolverInterface {

	/**
	 * The database connection factory instance.
	 *
	 * @var Illuminate\Database\Connectors\ConnectionFactory
	 */
	protected $factory;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * The available configurations for connections.
	 *
	 * @var array
	 */
	protected $configs = array();

	/**
	 * The name of the default connection.
	 *
	 * @var array
	 */
	protected $defaultConnection;

	protected $eventsDispatcher;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Illuminate\Foundation\Application  $app
	 * @param  Illuminate\Database\Connectors\ConnectionFactory  $factory
	 * @return void
	 */
	public function __construct(ConnectionFactory $factory, EventsDispatcher $eventsDispatcher)
	{
		$this->factory = $factory;
		$this->eventsDispatcher = $eventsDispatcher;
	}

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return Illuminate\Database\Connection
	 */
	public function connection($name = null)
	{
		$name = $name ?: $this->getDefaultConnection();

		// If we haven't created this connection, we'll create it based on the config
		// provided in the application. Once we've created the connections we will
		// set the "fetch mode" for PDO which determines the query return types.
		if ( ! isset($this->connections[$name]))
		{
			$connection = $this->factory->make($this->getConfig($name));

			$this->connections[$name] = $this->prepare($connection);
		}

		return $this->connections[$name];
	}

	public function setConfig($name, array $config)
	{
		$this->configs[$name] = $config;

		if ($this->defaultConnection === null) {
			$this->defaultConnection = $name;
		}
	}

	/**
	 * Prepare the database connection instance.
	 *
	 * @param  Illuminate\Database\Connection  $connection
	 * @return Illuminate\Database\Connection
	 */
	protected function prepare(Connection $connection)
	{
		$connection->setFetchMode(\PDO::FETCH_CLASS);

		$connection->setEventDispatcher($this->eventsDispatcher);

		return $connection;
	}

	/**
	 * Get the configuration for a connection.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function getConfig($name = null)
	{
		$name = $name ?: $this->getDefaultConnection();

		// To get the database connection configuration, we will just pull each of the
		// connection configurations and get the configurations for the given name.
		// If the configuration doesn't exist, we'll throw an exception and bail.
		$configs = $this->configs;

		if (is_null($config = array_get($configs, $name)))
		{
			throw new \InvalidArgumentException("Database [$name] not configured.");
		}

		return $config;
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection()
	{
		return $this->defaultConnection;
	}

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name)
	{
		if ( ! is_string($config)) {
			throw new \UnexpectedValueException("Default config should be a string.");
		}

		if ( ! array_key_exists($name, $this->configs)) {
			throw new \UnexpectedValueException("Cannot set default config [$name] as configuration deos not exist.");
		}

		$this->defaultConnection = $name;
	}

	/**
	 * Dynamically pass methods to the default connection.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}