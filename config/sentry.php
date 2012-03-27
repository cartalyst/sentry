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

return array(

	/**
	 * Database instance to use
	 * Leave this null to use the default 'active' db instance
	 * To use any other instance, set this to any instance that's defined in APPPATH/config/db.php
	 */
	'db_instance' => null,

	/*
	 * Table Names
	 */
	'table' => array(
		'users'           => 'users',
		'groups'          => 'groups',
		'users_groups'    => 'users_groups',
		'users_metadata'  => 'users_metadata',
		'users_suspended' => 'users_suspended',
	),

	/*
	 * Session keys
	 */
	'session' => array(
		'user'     => 'sentry_user',
		'provider' => 'sentry_provider',
	),

	/*
	 * Default Authorization Column - username or email
	 */
	'login_column' => 'email',

	/*
	 * Support nested groups?
	 */
	'nested_groups' => true,

	/*
	 * Remember Me settings
	 */
	'remember_me' => array(

		/**
		 * Cookie name credentials are stored in
		 */
		'cookie_name' => 'sentry_rm',

		/**
		 * How long the cookie should last. (seconds)
		 */
		'expire' => 1209600, // 2 weeks
	),

	/**
	 * Limit Number of Failed Attempts
	 * Suspends a login/ip combo after a # of failed attempts for a set amount of time
	 */
	'limit' => array(

		/**
		 * enable limit - true/false
		 */
		'enabled' => true,

		/**
		 * number of attempts before suspensions
		 */
		'attempts' => 5,

		/**
		 * suspension length - minutes
		 */
		'time' => 15,
	),

	'hash' => array(

		/**
		 * Strategy to use
		 * look into classes/sentry/hash/strategy for available strategies ( or make/import your own )
		 * Must be in strategies below
		 */
		'strategy' => 'SimpleAuth',

		'convert'  => array(
			'enabled' => true,
			'from'    => 'Sha256',
		),

		/**
		 * Available Strategies for your app
		 * This is used to set settings for conversion, like switching from SimpleAuth hashing to Sha256 or vice versa
		 */
		'strategies' => array(
			/**
			 * config options needed for hashing
			 * example:
			 * 'Strategy' => array(); // additional options needed for password hashing in your driver like a configurable salt
			 */

			'Sha256' => array(),

			'SimpleAuth' => array(

				// simpleauth salt of your last app if any
				'salt' => 'aaaa',
			),
		),
	),

);
