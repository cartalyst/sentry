<?php

return array(

	/* Table Names */
	'table' => array(
		'users' => 'users',
		'groups' => 'groups',
		'user_groups' => 'user_groups',
		'users_suspended' => 'users_suspended',
	),

	/* Default Authorization Column - username or email */
	'login_id' => 'username',

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

);
