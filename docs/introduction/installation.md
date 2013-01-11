###Installation

----------

#### Installing In Laravel 4 (with Composer)

There are four simple steps to install Sentry into Laravel 4:

1. Add `"cartalyst/sentry": "2.0.*"` to the `require` attribute of your `composer.json` *(requires you run `php composer.phar update` from the command line)*  
2. Add `Cartalyst\Sentry\SentryServiceProvider` to the list of service providers in `app/config/app.php`
3. Add `'Sentry' => 'Cartalyst\Sentry\Facades\Sentry'` to the list of class aliases in `app/config/app.php` *(optional)*
4. If you'd like to migrate tables, simply run `php artisan migrate --package=cartalyst/sentry` from the command line. Of course, feel free to write your own migrations which insert the correct tables if you'd like!


#### Installing Using Composer

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

$hasher = new Cartalyst\Sentry\Hashing\BcryptHasher; // There are other hashers available, take your pick

$illuminateSession = /* Get instance of Illuminate\Session\Store however pleases you */;
$session = new Cartalyst\Sentry\Sessions\IlluminateSession($illuminateSession);

$illuminateCookie = /* Get instance of Illuminate\Cookie\CookieJar however pleases you */;
$cookie = new Cartalyst\Sentry\Cookies\IlluminateCookie($illuminateCookie);

$groupProvider = new Cartalyst\Sentry\Groups\Eloquent\GroupProvider;

$userProvider = new Cartalyst\Sentry\Users\Eloquent\UserProvider($hasher);

$throttleProvider = new Cartalyst\Sentry\Throttling\Eloquent\ThrottleProvider($userProvider);

$sentry = new Sentry(
	$hasher,
	$session,
	$cookie,
	$groupProvider,
	$userProvider,
	$throttleProvider
);
```