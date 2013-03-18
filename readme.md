# Sentry

Sentry is a PHP 5.3+ fully-featured authentication & authorization system. It also provides additional features such as user groups and additional security features.

Sentry is a framework agnostic set of interfaces with default implementations, though you can substitute any implementations you see fit.

[![Build Status](https://travis-ci.org/cartalyst/sentry.png?branch=master)](https://travis-ci.org/cartalyst/sentry)

### Features

It also provides additional features such as user groups and additional security features:

- Configurable authentication (can use any type of authentication required, such as username or email)
- Authorization
- Activation of user *(optional)*
- Groups and group permissions
- "Remember me"
- User suspension
- Login throttling *(optional)*
- User banning
- Password resetting
- User data
- Interface driven - switch out your own implementations at will

### A Quick Note With Installing

You must ensure your minimum stability to either "beta", "alpha" or "dev" now:

```json
{
	"minimum-stability": "dev"
}
```

- "beta" will get you all beta / RC releases
- "dev" will keep you on top of the bleeding edge releases

Once the package is released you can change this flag back to "stable" or remove it.

### Installing In Laravel 4 (with Composer)

There are four simple steps to install Sentry into Laravel 4:

1. Add `"cartalyst/sentry": "2.0.*"` to the `require` attribute of your `composer.json` *(requires you run `php composer.phar update` from the command line)*
2. Add `Cartalyst\Sentry\SentryServiceProvider` to the list of service providers in `app/config/app.php`
3. Add `'Sentry' => 'Cartalyst\Sentry\Facades\Laravel\Sentry'` to the list of class aliases in `app/config/app.php` *(optional)*
4. If you'd like to migrate tables, simply run `php artisan migrate --package=cartalyst/sentry` from the command line. Of course, feel free to write your own migrations which insert the correct tables if you'd like!


### Installing in FuelPHP 1.x (with Composer)

Using Sentry with FuelPHP is easy. We begin by creating a file at `fuel/app/composer.json` with the following:

```json
{
	"require": {
		"cartalyst/sentry": "2.0.*",
		"illuminate/database": "4.0.*",
		"ircmaxell/password-compat": "1.0.*"
	},
	"minimum-stability": "dev"
}
```

Navigate to your `app` folder in Terminal and run `composer update`. You must put the following in `app/bootstrap.php` below `Autoloader::register()`:

```php
// Enable composer based autoloading
require APPPATH.'vendor/autoload.php';
```

Great! You now have composer working with FuelPHP. Just one more step is involved now, right at the bottom of that same file, `app/bootstrap.php`, put the following:

```php
class_alias('Cartalyst\Sentry\Facades\FuelPHP\Sentry', 'Sentry');
```

This will mean you can use the FuelPHP Sentry facade as the class `Sentry`. Vòila! Sentry automatically works with your current database configuration, there is no further setup required.

#### A small note

Sentry will always run off the default database connection, so ensure this is working. We may look at adding support for alternate connections in the future however it is not implemented at this stage. Pull reqeusts welcome.


### Installing in CodeIgniter 3.0-dev+ (with Composer)

Installation in CodeIgniter is fairly straightforward. Obviously, using Sentry in CodeIgniter brings the minimum PHP version to 5.3.0. To install, add the following to your `composer.json` file:

```json
{
	"require": {
		"cartalyst/sentry": "2.0.*",
		"illuminate/database": "4.0.*",
		"ircmaxell/password-compat": "1.0.*"
	},
	"minimum-stability": "dev"
}
```

> **Note**: You may need to merge the `require` attribute with that you already have in `composer.json`

Now, simply run `composer update`. Then, visit `application/config/config.php` and right down the bottom, add the following:

```php
class_alias('Cartalyst\Sentry\Facades\CI\Sentry', 'Sentry');
```

This will allow you to use Sentry as normal in CodeIgniter and sets up dependencies required for Sentry to run smoothly within the CI environment.

#### A small note

You must be running your database using the `PDO` driver (though this would be recommended anyway). Configuration for a MySQL database running PDO could be as follows (in `application/config/database.php`):

```php

// Ensure the active group is the default config.
// Sentry always runs off your application's default
// database connection.
$active_group = 'default';

// Setup the default config
$db['default'] = array(

	// PDO requires the host, dbname and charset are all specified in the "dsn",
	// so we'll go ahead and do these now.
	'dsn'	=> 'mysql:host=localhost;dbname=cartalyst_sentry;charset=utf8;',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => 'root',
	'database' => '',
	'dbdriver' => 'pdo',
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'autoinit' => TRUE,
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array()
);
```

### Installing Using Composer

Ensure you have the following in your `composer.json` file:

```json
{
	"require": {
		"cartalyst/sentry": "2.0.*",
		"illuminate/database": "4.0.*",
		"ircmaxell/password-compat": "1.0.*"
	}
}
```

```php

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
```


### Installing Using Composer (Customization example)

```json
{
	"require": {
		"cartalyst/sentry": "2.0.*"
	}
}
```

You heard us say how Sentry is completely interface driven? We have a number of implementations already built in for using Sentry which require the following `composer.json` file:

```json
{
	"require": {
		"cartalyst/sentry": "2.0.*",
		"illuminate/database": "4.0.*",
		"ircmaxell/password-compat": "1.0.*"
	}
}
```

Now run `php composer.phar update` from the command line.

Initializing Sentry requires you pass a number of dependencies to it. These dependencies are the following:

1. A hasher (must implement `Cartalyst\Sentry\Hashing\HasherInterface`).
2. A session manager (must implement `Cartalyst\Sentry\Sessions\SessionInterface`).
3. A cookie manager (must implement `Cartalyst\Sentry\Cookies\CookieInterface`).
4. A group provider (must implement `Cartalyst\Sentry\Groups\ProviderInterface`).
5. A user provider (must implement `Cartalyst\Sentry\Users\ProviderInterface`).
6. A throtte provider (must implement `Cartalyst\Sentry\Throttling\ProviderInterface`).

Of course, we provide default implementations of all these for you. To setup our default implementations, the following should suffice:

```php
<?php

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
```

We have plans to add more implementations for each interface which gives you more choice on how you would like to setup Sentry. In the mean-time, if you have would like to share an implementation you have made with us please send through a pull request and we'll be ever so grateful.

We're looking at making:

1. Plain PDO based group, user and throttle providers.

### Docs

Documentation is coming as we near a stable release of Sentry 2.0. Until then, you may check our [docs](https://github.com/cartalyst/sentry/tree/master/docs) folder, however some of the methods here may have changed and the docs *may* need updating. Bonus points go to any pull requests to help out with documentation.
