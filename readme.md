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

You must ensure your minimum stability to either "alpha" or "dev" now:

```json
{
	"minimum-stability": "dev"
}
```

- "alpha" will get you all alpha / beta / RC releases
- "dev" will keep you on top of the bleeding edge releases

Once the package is released you can change this flag back to "stable" or remove it.

### Installing In Laravel 4 (with Composer)

There are four simple steps to install Sentry into Laravel 4:

1. Add `"cartalyst/sentry": "2.0.*"` to the `require` attribute of your `composer.json` *(requires you run `php composer.phar update` from the command line)*  
2. Add `Cartalyst\Sentry\SentryServiceProvider` to the list of service providers in `app/config/app.php`
3. Add `'Sentry' => 'Cartalyst\Sentry\Facades\Laravel\Sentry'` to the list of class aliases in `app/config/app.php` *(optional)*
4. If you'd like to migrate tables, simply run `php artisan migrate --package=cartalyst/sentry` from the command line. Of course, feel free to write your own migrations which insert the correct tables if you'd like!


### Installing Using Composer

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
		"illuminate/cookie": "1.2.*",
        "illuminate/database": "1.2.*",
        "dhorrigan/capsule": "2.0.*",
        "illuminate/session": "1.2.*"
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

$illuminateSession = /* Get instance of Illuminate\Session\Store however pleases you */;
$session = new Cartalyst\Sentry\Sessions\IlluminateSession($illuminateSession);

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

1. A native PHP session manager (that uses cookies or memcache?).
2. Plain PDO based group, user and throttle providers.

### Docs

Documentation is coming as we near a stable release of Sentry 2.0. Until then, you may check our [docs](https://github.com/cartalyst/sentry/tree/master/docs) folder, however some of the methods here may have changed and the docs *may* need updating. Bonus points go to any pull requests to help out with documentation.