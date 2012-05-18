# Sentry

Sentry is a simple, easy to use authorization and authentication package built for Laravel.
It also provides additional features such as user groups and additional security features.

###Quickstart

* Clone sentry into *APPPATH/bundles/*
  * ```git clone https://github.com/cartalyst/sentry-laravel.git sentry```
* Edit *APPPATH/application/bundles.php*

```php

<?php
// APPPATH/application/bundles.php
return array(
  'sentry' => array('auto' => true),
);
```
* Edit your *APPPATH/application/config/db.php* file and make sure your database credentials are valid
* Run ```php artisan migrate sentry```
* [Begin using Sentry!](http://sentry.cartalyst.com/manual/v1.1.html)

###Features

* Authentication (via username or email)
* Authorization
* Groups / Roles
* Remember Me
* User Suspension / Login Attempt Limiter
* Password Reset
* User Activation
* User Metadata

###Docs

<!-- [http://sentry.cartalyst.com/manual/v1.1.html](http://sentry.cartalyst.com/manual/v1.1.html) -->
