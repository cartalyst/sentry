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
 	public static function forge($strategy, $options = array())
	{
		if ($strategy === null or empty($strategy))
		{
			throw new \SentryUserException(__('sentry.hash_strategy_null'));
		}

		echo 'hi';
		exit;

		$class = '\\Sentry\\Hash_Strategy_'.$strategy;

		return new $class($options);
	}

	abstract public function __construct($options);

 	// creates the password
 	abstract public function create_password($password);

 	// checks the password
 	abstract public function check_password($password, $hashed_password);

 }
