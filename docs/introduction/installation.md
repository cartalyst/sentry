###Installation

----------

Once downloaded, you will probably want to autoload the bundle by adding the following code to your application bundles.php file.

	'sentry' => array(
		'location' => 'path: path/to/sentry',
		'auto'     => true,
	),

Installing the tables for sentry is as simple as running its migration.


	php artisan migrate sentry

<br>
>**Note:** If you wish to change the default table names, you may adjust them in the configuration file.
