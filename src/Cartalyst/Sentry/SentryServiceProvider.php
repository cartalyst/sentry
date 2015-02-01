<?php namespace Cartalyst\Sentry;
/**
 * Part of the Sentry package.
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
 * @version    2.0.0
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
use Cartalyst\Sentry\Hashing\WhirlpoolHasher;
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
	 */
	public function register()
	{
		$this->prepareResources();
		$this->registerHasher();
		$this->registerUserProvider();
		$this->registerGroupProvider();
		$this->registerThrottleProvider();
		$this->registerSession();
		$this->registerCookie();
		$this->registerSentry();
	}

	/**
	 * Prepare the package resources.
	 *
	 * @return void
	 */
	protected function prepareResources()
	{
		$config     = realpath(__DIR__.'/../../config/config.php');
		$migrations = realpath(__DIR__.'/../../migrations');

		$this->mergeConfigFrom($config, 'cartalyst.sentry');

		$this->publishes([
			$config     => config_path('cartalyst.sentry.php'),
			$migrations => $this->app->databasePath().'/migrations',
		]);
	}

	/**
	 * Register the hasher used by Sentry.
	 *
	 * @return void
	 */
	protected function registerHasher()
	{
		$this->app['sentry.hasher'] = $this->app->share(function($app)
		{
			$hasher = $app['config']->get('cartalyst.sentry.hasher');

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

				case 'whirlpool':
					return new WhirlpoolHasher;
					break;
			}

			throw new \InvalidArgumentException("Invalid hasher [$hasher] chosen for Sentry.");
		});
	}

	/**
	 * Register the user provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerUserProvider()
	{
		$this->app['sentry.user'] = $this->app->share(function($app)
		{
			$config = $app['config']->get('cartalyst.sentry');

			$model = array_get($config, 'users.model');

			// We will never be accessing a user in Sentry without accessing
			// the user provider first. So, we can lazily set up our user
			// model's login attribute here. If you are manually using the
			// attribute outside of Sentry, you will need to ensure you are
			// overriding at runtime.
			if (method_exists($model, 'setLoginAttributeName'))
			{
				$loginAttribute = array_get($config, 'users.login_attribute');

				forward_static_call_array(
					array($model, 'setLoginAttributeName'),
					array($loginAttribute)
				);
			}

			// Define the Group model to use for relationships.
			if (method_exists($model, 'setGroupModel'))
			{
				$groupModel = array_get($config, 'groups.model');

				forward_static_call_array(
					array($model, 'setGroupModel'),
					array($groupModel)
				);
			}

			// Define the user group pivot table name to use for relationships.
			if (method_exists($model, 'setUserGroupsPivot'))
			{
				$pivotTable = array_get($config, 'user_groups_pivot_table');

				forward_static_call_array(
					array($model, 'setUserGroupsPivot'),
					array($pivotTable)
				);
			}

			return new UserProvider($app['sentry.hasher'], $model);
		});
	}

	/**
	 * Register the group provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerGroupProvider()
	{
		$this->app['sentry.group'] = $this->app->share(function($app)
		{
			$config = $app['config']->get('cartalyst.sentry');

			$model = array_get($config, 'groups.model');

			// Define the User model to use for relationships.
			if (method_exists($model, 'setUserModel'))
			{
				$userModel = array_get($config, 'users.model');

				forward_static_call_array(
					array($model, 'setUserModel'),
					array($userModel)
				);
			}

			// Define the user group pivot table name to use for relationships.
			if (method_exists($model, 'setUserGroupsPivot'))
			{
				$pivotTable = array_get($config, 'user_groups_pivot_table');

				forward_static_call_array(
					array($model, 'setUserGroupsPivot'),
					array($pivotTable)
				);
			}

			return new GroupProvider($model);
		});
	}

	/**
	 * Register the throttle provider used by Sentry.
	 *
	 * @return void
	 */
	protected function registerThrottleProvider()
	{
		$this->app['sentry.throttle'] = $this->app->share(function($app)
		{
			$config = $app['config']->get('cartalyst.sentry');

			$model = array_get($config, 'throttling.model');

			$throttleProvider = new ThrottleProvider($app['sentry.user'], $model);

			if (array_get($config, 'throttling.enabled') === false)
			{
				$throttleProvider->disable();
			}

			if (method_exists($model, 'setAttemptLimit'))
			{
				$attemptLimit = array_get($config, 'throttling.attempt_limit');

				forward_static_call_array(
					array($model, 'setAttemptLimit'),
					array($attemptLimit)
				);
			}
			if (method_exists($model, 'setSuspensionTime'))
			{
				$suspensionTime = array_get($config, 'throttling.suspension_time');

				forward_static_call_array(
					array($model, 'setSuspensionTime'),
					array($suspensionTime)
				);
			}

			// Define the User model to use for relationships.
			if (method_exists($model, 'setUserModel'))
			{
				$userModel = array_get($config, 'users.model');

				forward_static_call_array(
					array($model, 'setUserModel'),
					array($userModel)
				);
			}

			return $throttleProvider;
		});
	}

	/**
	 * Register the session driver used by Sentry.
	 *
	 * @return void
	 */
	protected function registerSession()
	{
		$this->app['sentry.session'] = $this->app->share(function($app)
		{
			$key = $app['config']->get('cartalyst.sentry.cookie.key');

			return new IlluminateSession($app['session.store'], $key);
		});
	}

	/**
	 * Register the cookie driver used by Sentry.
	 *
	 * @return void
	 */
	protected function registerCookie()
	{
		$this->app['sentry.cookie'] = $this->app->share(function($app)
		{
			$key = $app['config']->get('cartalyst.sentry.cookie.key');

			/**
			 * We'll default to using the 'request' strategy, but switch to
			 * 'jar' if the Laravel version in use is 4.0.*
			 */

			$strategy = 'request';

			if (preg_match('/^4\.0\.\d*$/D', $app::VERSION))
			{
				$strategy = 'jar';
			}

			return new IlluminateCookie($app['request'], $app['cookie'], $key, $strategy);
		});
	}

	/**
	 * Takes all the components of Sentry and glues them
	 * together to create Sentry.
	 *
	 * @return void
	 */
	protected function registerSentry()
	{
		$this->app['sentry'] = $this->app->share(function($app)
		{
			return new Sentry(
				$app['sentry.user'],
				$app['sentry.group'],
				$app['sentry.throttle'],
				$app['sentry.session'],
				$app['sentry.cookie'],
				$app['request']->getClientIp()
			);
		});

		$this->app->alias('sentry', 'Cartalyst\Sentry\Sentry');
	}

}
