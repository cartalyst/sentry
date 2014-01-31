<?php namespace Cartalyst\Sentry\Native;

use ArrayAccess;

class ConfigRepository implements ArrayAccess {

	protected $file;

	protected $config = array();

	public function __construct($file = null)
	{
		$this->file = $file ?: __DIR__.'/../../../config/config.php';

		$this->load();
	}

	protected function load()
	{
		$this->config = require $this->file;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists($key)
	{
		return isset($this->config[$key]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet($key)
	{
		return $this->config[$key];
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet($key, $value)
	{
		$this->config[$key] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset($key)
	{
		unset($this->config[$key]);
	}

}
