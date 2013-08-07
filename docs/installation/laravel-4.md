### Installing in Laravel 4 (with Composer)

**There are four simple steps to install Sentry into Laravel 4:**

##### Step 1

Add `"cartalyst/sentry": "2.0.*"` to the `require` attribute of your `composer.json`
(requires you to run `php composer.phar update` from the command line)

##### Step 2

Add `'Cartalyst\Sentry\SentryServiceProvider'` to the list of service providers in `app/config/app.php`

##### Step 3  *(optional)*

Add `'Sentry' => 'Cartalyst\Sentry\Facades\Laravel\Sentry'` to the list of class aliases in `app/config/app.php`

##### Step 4

If you'd like to migrate tables, simply run `php artisan migrate --package=cartalyst/sentry` from the command line. Of course, feel free to write your own migrations which insert the correct tables if you'd like!

##### Step 5  *(optional)*

If you'd like to inject Sentry as a dependency you need to register Sentry manually inside the IoC container. Add `$app['Cartalyst\Sentry\Sentry'] = $app['sentry'];` to `app/start/global.php`. 

Example usage:

```
class UserRepository{

	function __construct(Sentry $sentry)
	{
		$this->sentry = $sentry;
	}
}
```

##### Step 6  *(optional)*

If you want to change Sentry's settings (hashing type, User/Group model etc.), you need to publish its config file(s).

Run `php artisan config:publish cartalyst/sentry` from the command line and you should find the files in `app/config/packages/cartalyst/sentry`.