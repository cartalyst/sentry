## Installing in Kohana 3.3.x (with Composer)

Using Sentry with Kohhana is easy if you have composer enabled on your project.

Open `composer.json` (put it in you Kohana's project root) and add these lines:

	{
		"require": {
			"cartalyst/sentry": "2.0.*",
			"ircmaxell/password-compat": "1.0.*",
			"happydemon/txt": "1.0.1"
		},
		"minimum-stability": "dev"
	}

Only the Kohana port of Sentry needs the `happydemon/txt` package since it uses a helper that does not come bundled with Kohana by default.

Next navigate to your `kohana`'s project folder in the terminal and run `composer update`.

IF you haven't activated composer's autoloader already, you should put the following in `application/bootstrap.php` below `spl_autoload_register(array('Kohana', 'auto_load'));`:

	// Enable composer based autoloading
	require APPPATH.'vendor/autoload.php';

Great! You now have composer working with Kohana.

Just one more step is involved now, right at the bottom of that same file, `application/bootstrap.php`, put the following:

	class_alias('Cartalyst\Sentry\Facades\Kohana\Sentry', 'Sentry');

This will mean you can use the Kohana Sentry facade as the class `Sentry`. 

Lastly you can add a `Sentry` config file to optionally change the default configuration. You would have to create a config file called `application/config/sentry.php` and paste in this piece of code:

    <?php defined('SYSPATH' OR die('No direct access allowed.'));
    /**
     * Sentry config file
     */
    return array(
    	/**
    	 * Sentry specific setup
    	 */
    	'session_driver' => 'native', //native or database @see Kohana_Session::instance()
    	'session_key' => 'cartalyst_sentry',
    	'cookie_key' => 'cartalyst_sentry',
    	'hasher' => 'Bcrypt' //Bcrypt, Native or Sha256
    );

Voilà! Sentry automatically works with your current database configuration, there is no further setup required.

> **Note**: Sentry will always run off the default database connection, so ensure this is working. We may look at adding support for alternate connections in the future however it is not implemented at this stage. Pull requests are welcome.
