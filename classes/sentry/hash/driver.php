<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

 namespace Sentry;

 abstract class Hash_Driver
 {
 	public static function forge($strategy, $options = array())
	{
		$class = '\\Sentry\\Hash_Strategy_'.\Inflector::classify($strategy);

		return new $class($options);
	}

	abstract public function __construct($options);

 	// creates the password
 	abstract public function create_password($password);

 	// checks the password
 	abstract public function check_password($password, $hashed_password);

 }
