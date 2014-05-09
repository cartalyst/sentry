# Introduction

A modern and framework agnostic authorization and authentication package featuring groups, permissions, custom hashing algorithms and additional security features.

The package follows the FIG standard PSR-0 to ensure a high level of interoperability between shared PHP code.

The package requires PHP 5.3+ and comes bundled with a Laravel 4 Facade and a Service Provider to simplify the optional framework integration.

Have a [read through the Installation Guide](#installation) and on how to [Integrate it with Laravel 4](#laravel-4).

### Quick Example

#### Create a user

```php
Sentry::register(array(
	'email'    => 'john.doe@example.com',
	'password' => 'foobar',
));
```

#### Authenticate a user

```php
Sentry::authenticate(array(
	'email'    => 'john.doe@example.com',
	'password' => 'foobar',
));
```

#### Create a group

```php
Sentry::createGroup(array(
	'name'        => 'Subscribers',
	'permissions' => array(
		'admin' => 1,
		'users' => 1,
	),
));
```
