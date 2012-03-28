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
		'enabled' => false,

		/**
		 * number of attempts before suspensions
		 */
		'attempts' => 5,

		/**
		 * suspension length - minutes
		 */
		'time' => 15,
	),

	/**
	 * Password Hashing
	 * Sets hashing strategies for passwords
	 * Note: you may have to adjust all password related fields in the database depending on the password hash length
	 */
	'hash' => array(

		/**
		 * Strategy to use
		 * look into classes/sentry/hash/strategy for available strategies ( or make/import your own )
		 * Must be in strategies below
		 */
		'strategy' => 'Sentry',

		'convert'  => array(
			'enabled' => null,
			'from'    => '',
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

			'Sentry' => array(),

			'SimpleAuth' => array(
				'salt' => '',
			),

			'BCrypt' => array(
				'strength' => 4,
				// if you want to use a bacrypt hash with an algorithm
				'hashing_algorithm' => null,
			),
		),
	),

	'permissions' => array(

		/**
		 * enable permissions - true or false
		 */
		'enabled' => true,

		/**
		 * super user - string
		 * this will be used for the group and rules
		 * if you change this, you need to make sure you change the
		 */
		'superuser' => 'superuser',

		/**
		 * setup rules for permissions
		 * These are resources that will require access permissions.
		 * Rules are assigned to groups or specific users in the
		 * format module_controller_method or controller_method
		 */
		'rules' => array(
			/**
			 * config samples.
			 *
			 *	// user module admin
			 *	'user_admin_create',
			 *	'user_admin_read',
			 *	'user_admin_update',
			 *	'user_admin_delete',
			 *	'user_permissions',
			 *
			 *	// blog module admin
			 *	'blog_admin_create',
			 *	'blog_admin_read',
			 *	'blog_admin_update',
			 *	'blog_admin_delete',
			 *
			 *	// product module admin
			 *	'product_admin_create',
			 *	'product_admin_read',
			 *	'product_admin_update',
			 *	'product_admin_delete',
			 */

		)
	)
);
