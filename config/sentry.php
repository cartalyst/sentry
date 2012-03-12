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

	'permissions' => array(

		/**
		 * enable permissions - true or false
		 */
		'enabled' => true,

		/**
		 * setup rules for permissions
		 * These are resources that will require access permissions.
		 * Rules are assigned to groups or specific users in the
		 * format module_controller_method or controller_method
		 */
		'rules' => array(
			// user module admin
			'user_admin_create',
			'user_admin_read',
			'user_admin_update',
			'user_admin_delete',

			// blog module admin
			'blog_admin_create',
			'blog_admin_read',
			'blog_admin_update',
			'blog_admin_delete',

			// product module admin
			'product_admin_create',
			'product_admin_read',
			'product_admin_update',
			'product_admin_delete',
		)
	)
);
