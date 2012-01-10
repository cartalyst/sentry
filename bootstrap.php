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

Autoloader::add_core_namespace('Sentry');

Autoloader::add_classes(array(
	'Sentry\\Sentry'                              => __DIR__.'/classes/sentry.php',
	'Sentry\\SentryAuthException'                 => __DIR__.'/classes/sentry.php',
	'Sentry\\SentryAuthUserNotActivatedException' => __DIR__.'/classes/sentry.php',
	'Sentry\\SentryAuthConfigException'           => __DIR__.'/classes/sentry.php',

	'Sentry\\Sentry_Attempts'                     => __DIR__.'/classes/sentry/attempts.php',
	'Sentry\\SentryAttemptsException'             => __DIR__.'/classes/sentry/attempts/php',
	'Sentry\\SentryUserSuspendedException'        => __DIR__.'/classes/sentry/attempts.php',

	'Sentry\\Sentry_User'                         => __DIR__.'/classes/sentry/user.php',
	'Sentry\\SentryUserException'                 => __DIR__.'/classes/sentry/user.php',
	'Sentry\\SentryUserNotFoundException'         => __DIR__.'/classes/sentry/user.php',

	'Sentry\\Sentry_Group'                        => __DIR__.'/classes/sentry/group.php',
	'Sentry\\SentryGroupException'                => __DIR__.'/classes/sentry/group.php',
	'Sentry\\SentryGroupNotFoundException'        => __DIR__.'/classes/sentry/group.php',
));
