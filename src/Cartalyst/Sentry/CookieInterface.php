<?php namespace Cartalyst\Sentry;

interface CookieInterface
{
	public function getKey();

	public function put($key, $value, $minutes);

	public function forever($key, $value);

	public function get($key, $default = null);

	public function forget($key);

	public function flush();
}