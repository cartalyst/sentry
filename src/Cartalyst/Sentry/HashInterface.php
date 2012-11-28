<?php namespace Cartalyst\Sentry;

interface HashInterface
{
	/**
	 * Hash String
	 *
	 * @param   string  $str
	 * @return  string
	 */
	public function hash($str);

	/**
	 * Check Hash Values
	 *
	 * @param   string  $str
	 * @param   string  $hashed_str
	 * @return  bool
	 */
	public function checkhash($str, $hashed_str);
}