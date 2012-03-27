<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

 namespace Sentry;

 abstract class Hash_Driver
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
			throw new \SentryAuthException(__('sentry.hash_strategy_null'));
		}

		$class = '\\Sentry\\Hash_Strategy_'.$strategy;

		return new $class($options);
	}

	// required constructor for passing options
	abstract public function __construct($options);

 	// creates the password
 	abstract public function create_password($password);

 	// checks the password
 	abstract public function check_password($password, $hashed_password);

 }
