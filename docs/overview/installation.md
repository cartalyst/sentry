### Installation

Once you have downloaded Sentry, you will probably want to autoload the bundle
by adding the following code to your application bundles.php file.

	'sentry' => array('auto' => true),

Installing the tables for Sentry is as simple as running its migrations.

	php artisan migrate sentry

> **Note:** If you wish to change the default table names, you may adjust them
in the configuration file, before running the migrations.
