<?php namespace Cartalyst\Sentry\Cookie;

use Cartalyst\Sentry\CookieInterface;
use Illuminate\CookieJar;
use Session;

class Laravel implements CookieInterface
{
	protected $key = 'sentryRemember';

	protected $cookie;

	public function __construct(CookieJar $cookieDriver)
	{
		$this->cookie = $cookieDriver;
	}

	public function getKey()
	{
		return $this->key;
	}

	public function put($key, $value, $minutes)
	{
		return $this->cookie->put($key, $value, $minutes);
	}

	public function forever($key, $value)
	{
		return $this->cookie->forever($key, $value);
	}

	public function get($key, $default = null)
	{
		return $this->cookie->get($key, $default);
	}

	public function forget($key)
	{
		return $this->cookie->forget($key);
	}

	public function flush()
	{
		return $this->forget($this->key);
	}
}