<?php namespace Cartalyst\Sentry;

interface HashInterface
{
	public function hash($str);

	public function checkhash($str, $hashed_str);
}