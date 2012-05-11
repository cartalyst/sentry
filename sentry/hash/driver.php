<?php
/**
 * Part of the Sentry package for Laravel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

 namespace Sentry;

/**
 * Hashing Driver
 */
abstract class Sentry_Hash_Driver
{
	/**
	 * @var  array  array of configurable options for simpleauth hashing
	 */
	protected $options = array();

	/**
	 * Creates and return hashing strategy object
	 */
	public static function forge($strategy, $options = array())
	{
		if ($strategy === null or empty($strategy))
		{
			throw new SentryException(__('sentry::sentry.hash_strategy_null'));
		}

		$class = 'Sentry\\Sentry_Hash_Strategy_'.$strategy;

		return new $class($options);
	}

	// required constructor for passing options
	abstract public function __construct($options);

	// creates the password
	abstract public function create_password($password);

	// checks the password
	abstract public function check_password($password, $hashed_password);

}
