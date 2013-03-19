### Installing using Composer

Ensure you have the following in your `composer.json` file:

	{
		"require": {
			"cartalyst/sentry": "2.0.*",
			"illuminate/database": "4.0.*",
			"ircmaxell/password-compat": "1.0.*"
		}
	}


Example usage

	// Create an alias for our Facade
	class_alias('Cartalyst\Sentry\Facades\Native\Sentry', 'Sentry');

	// Setup our database
	$dsn      = 'mysql:dbname=my_database;host=localhost';
	$user     = 'root';
	$password = 'password';
	Sentry::setupDatabaseResolver(new PDO($dsn, $user, $password));

	// Done!

	// Create our first user!
	$user = Sentry::getUserProvider()->create(array(
		'email'    => 'testing@test.com',
		'password' => 'test',
		'permissions' => array(
			'test'  => 1,
			'other' => -1,
			'admin' => 1
		)
	));

	var_dump($user);

----------

### Installing Using Composer (Customization example)

	{
		"require": {
			"cartalyst/sentry": "2.0.*"
		}
	}

You heard us say how Sentry is completely interface driven? We have a number of implementations already built in for using Sentry which require the following `composer.json` file:

	{
		"require": {
			"cartalyst/sentry": "2.0.*",
			"illuminate/database": "4.0.*",
			"ircmaxell/password-compat": "1.0.*"
		}
	}

Now run `php composer.phar update` from the command line.

Initializing Sentry requires you pass a number of dependencies to it. These dependencies are the following:

1. A hasher (must implement `Cartalyst\Sentry\Hashing\HasherInterface`).
2. A session manager (must implement `Cartalyst\Sentry\Sessions\SessionInterface`).
3. A cookie manager (must implement `Cartalyst\Sentry\Cookies\CookieInterface`).
4. A group provider (must implement `Cartalyst\Sentry\Groups\ProviderInterface`).
5. A user provider (must implement `Cartalyst\Sentry\Users\ProviderInterface`).
6. A throtte provider (must implement `Cartalyst\Sentry\Throttling\ProviderInterface`).

Of course, we provide default implementations of all these for you. To setup our default implementations, the following should suffice:

	$hasher = new Cartalyst\Sentry\Hashing\NativeHasher; // There are other hashers available, take your pick

	$session = new Cartalyst\Sentry\Sessions\NativeSession;

	// Note, all of the options below are, optional!
	$options = array(
		'name'     => null, // Default "cartalyst_sentry"
		'time'     => null, // Default 300 seconds from now
		'domain'   => null, // Default ""
		'path'     => null, // Default "/"
		'secure'   => null, // Default "false"
		'httpOnly' => null, // Default "false"
	);
	$cookie = new Cartalyst\Sentry\Cookies\NativeCookie($options);

	$groupProvider = new Cartalyst\Sentry\Groups\Eloquent\Provider;

	$userProvider = new Cartalyst\Sentry\Users\Eloquent\Provider($hasher);

	$throttleProvider = new Cartalyst\Sentry\Throttling\Eloquent\Provider($userProvider);

	$sentry = new Sentry(
		$hasher,
		$session,
		$cookie,
		$groupProvider,
		$userProvider,
		$throttleProvider
	);
