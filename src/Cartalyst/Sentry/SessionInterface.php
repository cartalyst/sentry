<?php namespace Cartalyst\Sentry;

interface SessionInterface
{
	public function getKey();

	public function put($key, $value);

	public function get($key, $default = null);

	public function forget($key);

	public function flush();
}