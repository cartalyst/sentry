<?php namespace Cartalyst\Sentry\Laravel;
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

use Cartalyst\Sentry\Activations\IlluminateActivationRepository;
use Cartalyst\Sentry\Cookies\IlluminateCookie;
use Cartalyst\Sentry\Checkpoints\ActivationCheckpoint;
use Cartalyst\Sentry\Checkpoints\ThrottleCheckpoint;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Persistence\SentryPersistence;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Sessions\IlluminateSession;
use Cartalyst\Sentry\Throttling\IlluminateThrottleRepository;
use Cartalyst\Sentry\Users\IlluminateUserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class SentryServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	protected $defer = true;

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->package('cartalyst/sentry', 'cartalyst/sentry', __DIR__.'/../../..');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerPersistence();
		$this->registerUsers();
		$this->registerCheckpoints();
		$this->registerSentry();
	}

	protected function registerPersistence()
	{
		$this->registerSession();
		$this->registerCookie();

		$this->app['sentry.persistence'] = $this->app->share(function($app)
		{
			return new SentryPersistence($app['sentry.session'], $app['sentry.cookie']);
		});
	}

	protected function registerSession()
	{
		$this->app['sentry.session'] = $this->app->share(function($app)
		{
			$key = $app['config']['cartalyst/sentry::session'];

			return new IlluminateSession($app['session.store'], $key);
		});
	}

	protected function registerCookie()
	{
		$this->app['sentry.cookie'] = $this->app->share(function($app)
		{
			$key = $app['config']['cartalyst/sentry::cookie'];

			return new IlluminateCookie($app['request'], $app['cookie'], $key);
		});

		$app = $this->app;
		$this->app->close(function(Request $request, Response $response) use ($app)
		{
			$cookie = $app['sentry.cookie']->getCookie();

			if ($cookie)
			{
				$response->headers->setCookie($cookie);
			}
		});
	}

	protected function registerUsers()
	{
		$this->registerHasher();

		$this->app['sentry.users'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::users.model'];

			return new IlluminateUserRepository($app['sentry.hasher'], $model);
		});
	}

	protected function registerHasher()
	{
		$this->app['sentry.hasher'] = $this->app->share(function($app)
		{
			return new NativeHasher;
		});
	}

	protected function registerCheckpoints()
	{
		$this->registerActivationCheckpoint();
		$this->registerThrottleCheckpoint();

		$this->app['sentry.checkpoints'] = $this->app->share(function($app)
		{
			$checkpoints = $app['config']['cartalyst/sentry::checkpoints'];

			$checkpoints = array_map(function($checkpoint) use ($app)
			{
				switch ($checkpoint)
				{
					case 'activation':
					case 'throttle':
						return $app["sentry.checkpoint.{$checkpoint}"];

					default:
						throw new \InvalidArgumentException("Invalid checkpoint [$checkpoint] given.");
				}
			}, $checkpoints);

			return $checkpoints;
		});
	}

	protected function registerActivationCheckpoint()
	{
		$this->registerActivation();

		$this->app['sentry.checkpoint.activation'] = $this->app->share(function($app)
		{
			return new ActivationCheckpoint($app['sentry.activation']);
		});
	}

	protected function registerActivation()
	{
		$this->app['sentry.activation'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::activation.model'];
			$expires = $app['config']['cartalyst/sentry::activation.expires'];

			return new IlluminateActivationRepository($model, $expires);
		});
	}

	protected function registerThrottleCheckpoint()
	{
		$this->registerThrottle();

		$this->app['sentry.checkpoint.throttle'] = $this->app->share(function($app)
		{
			return new ThrottleCheckpoint(
				$app['sentry.throttle'],
				$app['request']->getClientIp()
			);
		});
	}

	protected function registerThrottle()
	{
		$this->app['sentry.throttle'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::throttling.model'];

			foreach (array('global', 'ip', 'user') as $type)
			{
				${"{$type}Interval"} = $app['config']["cartalyst/sentry::throttling.{$type}.interval"];
				${"{$type}Thresholds"} = $app['config']["cartalyst/sentry::throttling.{$type}.thresholds"];
			}

			return new IlluminateThrottleRepository(
				$model,
				$globalInterval,
				$globalThresholds,
				$ipInterval,
				$ipThresholds,
				$userInterval,
				$userThresholds
			);
		});
	}

	protected function registerSentry()
	{
		$this->app['sentry'] = $this->app->share(function($app)
		{
			$sentry = new Sentry(
				$app['sentry.persistence'],
				$app['sentry.users'],
				$app['events']
			);

			if (isset($app['sentry.checkpoints']))
			{
				foreach ($app['sentry.checkpoints'] as $checkpoint)
				{
					$sentry->addCheckpoint($checkpoint);
				}
			}

			$sentry->setActivationsRepository($app['sentry.activation']);

			return $sentry;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function provides()
	{
		return array(
			'sentry.session',
			'sentry.cookie',
			'sentry.persistence',
			'sentry.hasher',
			'sentry.users',
			'sentry.activation',
			'sentry.checkpoint.activation',
			'sentry.throttle',
			'sentry.checkpoint.throttle',
			'sentry.checkpoints',
			'sentry',
		);
	}

}
