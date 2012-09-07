# Sentry

Sentry is a simple, easy to use authorization and authentication package built for Laravel.
It also provides additional features such as user groups and additional security features.

###Quickstart

* Clone sentry into *APPPATH/bundles/*
  * ```git clone https://github.com/cartalyst/sentry.git sentry```
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
* [Begin using Sentry!](http://getplatform.com/manuals/sentry)

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

<!-- [http://getplatform.com/manuals/sentry](http://getplatform.com/manuals/sentry) -->
