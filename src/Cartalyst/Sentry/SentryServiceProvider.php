<?php namespace Cartalyst\Sentry;
/**
 * Part of the Sentry Package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Cookies\IlluminateCookie;
use Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;
use Cartalyst\Sentry\Hashing\BcryptHasher;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Hashing\Sha256Hasher;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Sessions\IlluminateSession;
use Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
use Illuminate\Support\ServiceProvider;

class SentryServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 *
	 */
	public function register()
	{
		$this->package('cartalyst/sentry', 'cartalyst/sentry');

		$this->registerHasher();

		$this->registerSession();

		$this->registerCookie();

		$this->registerGroupProvider();

		$this->registerUserProvider();

		$this->registerThrottleProvider();

		$this->registerSentry();

		$this->registerEvents();
	}

	protected function registerHasher()
	{
		$this->app['sentry.hasher'] = $this->app->share(function($app)
		{
			$hasher = $app['config']['cartalyst/sentry::sentry.hasher'];

			switch ($hasher)
			{
				case 'native':
					return new NativeHasher;
					break;

				case 'bcrypt':
					return new BcryptHasher;
					break;

				case 'sha256':
					return new Sha256Hasher;
					break;
			}

			throw new \InvalidArgumentException("Invalid hasher [$hasher] chosen for Sentry.");
		});
	}

	protected function registerSession()
	{
		$this->app['sentry.session'] = $this->app->share(function($app)
		{
			return new IlluminateSession($app['session']);
		});
	}

	protected function registerCookie()
	{
		$this->app['sentry.cookie'] = $this->app->share(function($app)
		{
			return new IlluminateCookie($app['cookie']);
		});
	}

	protected function registerGroupProvider()
	{
		$this->app['sentry.group'] = $this->app->share(function($app)
		{
			$class = $app['config']['cartalyst/sentry::sentry.groups.model'];

			return new GroupProvider($class);
		});
	}

	protected function registerUserProvider()
	{
		$this->app['sentry.user'] = $this->app->share(function($app)
		{
			$class = $app['config']['cartalyst/sentry::sentry.users.model'];

			return new UserProvider($app['sentry.hasher'], $class);
		});
	}

	protected function registerThrottleProvider()
	{
		$this->app['sentry.throttle'] = $this->app->share(function($app)
		{
			$class = $app['config']['cartalyst/sentry::sentry.throttling.model'];

			$throttleProvider = new ThrottleProvider($app['sentry.user'], $class);

			if ($app['config']['cartalyst/sentry::sentry.throttling.enabled'] === false)
			{
				$throttleProvider->disable();
			}

			return $throttleProvider;
		});
	}

	protected function registerSentry()
	{
		$this->app['sentry'] = $this->app->share(function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['sentry.loaded'] = true;

			return new Sentry(
				$app['sentry.hasher'],
				$app['sentry.session'],
				$app['sentry.cookie'],
				$app['sentry.group'],
				$app['sentry.user'],
				$app['sentry.throttle']
			);
		});
	}

	protected function registerEvents()
	{
		// Set the cookie after the app runs
		$app = $this->app;
		$this->app->after(function($request, $response) use ($app)
		{
			if (isset($app['sentry.loaded']) and $app['sentry.loaded'] == true and ($cookie = $app['sentry.cookie']->getCookie()))
			{
				$response->headers->setCookie($cookie);
			}
		});
	}

}
