<?php
/**
 * Part of the Sentry bundle for Laravel.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
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

		/**
		 * Convert hashes from another available strategy
		 */
		'convert'  => array(
			'enabled' => false,
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

			'Oscommerce' => array(
				'salt' => '',
			),

			'BCrypt' => array(
				'strength' => 4,
				// if you want to use a bcrypt hash with an algorithm
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
		 * The permission rules file
		 * Must return an array with a 'rules' key.
		 */
		'file' => array(
			/**
			 * Type options: config | php
			 *
			 * name and path are ignored if type is config
			 * 	- name will be permissions
			 *	- path will be the bundles config folder
			 *
			 * name and path are required if type is php
			 *  - name will be the file name of the php file
			 *  - path will be relative to the current bundles base folder
			 */
			'type' => 'php',
			'name' => 'extension',
			'path' => '',
		),

		/**
		 * setup rules for permissions
		 * These are resources that will require access permissions.
		 * Rules are assigned to groups or specific users in the
		 * format module@controller::method
		 *
		 * This is always used for global permissions
		 */
		'rules' => array(
			/**
			 * config samples.
			 *
			 *	'application@admin::dashboard',
			 *	'user@admin::create',
			 *	'user@admin::read',
			 *	'blog@admin::delete',
			 *  'my_custom_rule',
			 *  'is_admin',
			 */
			'is_admin',
			'superuser',
		),

	),

);
