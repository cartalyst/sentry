<?php
/**
 * @package    Sentry Auth
 * @version    1.0
 * @author     Cartalyst Development Team
 * @link       http://cartalyst.com
 */


Autoloader::add_core_namespace('Sentry');

Autoloader::add_classes(array(
	'Sentry\\Sentry' => __DIR__.'/classes/sentry.php',
	'Sentry\\SentryAuthException' => __DIR__.'/classes/sentry.php',
	'Sentry\\SentryAuthConfigException' => __DIR__.'/classes/sentry.php',

	'Sentry\\Sentry_Attempts' => __DIR__.'/classes/sentry/attempts.php',
	'Sentry\\SentryUserSuspendedException' => __DIR__.'/classes/sentry/attempts.php',

	'Sentry\\Sentry_User' => __DIR__.'/classes/sentry/user.php',
	'Sentry\\SentryUserException' => __DIR__.'/classes/sentry/user.php',
	'Sentry\\SentryUserNotFoundException' => __DIR__.'/classes/sentry/user.php'
));
