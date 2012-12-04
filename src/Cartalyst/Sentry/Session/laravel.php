<?php namespace Cartalyst\Sentry\Session;

use Cartalyst\Sentry\SessionInterface;
use Illuminate\Session\Store as SessionStore;
use Session;

class Laravel implements SessionInterface
{
	protected $key = 'sentry';

	protected $session;

	public function __construct(SessionStore $sessionDriver)
	{
		$this->session = $sessionDriver;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function put($key, $value)
	{
		return $this->session->put($key, $value);
	}

	public function get($key, $default = null)
	{
		return $this->session->get($key, $default);
	}

	public function forget($key)
	{
		return $this->session->forget($key);
	}

	public function flush()
	{
		return $this->forget($this->key);
	}
}