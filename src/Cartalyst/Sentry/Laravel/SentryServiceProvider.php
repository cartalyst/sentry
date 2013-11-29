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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Activations\IlluminateActivationRepository;
use Cartalyst\Sentry\Cookies\IlluminateCookie;
use Cartalyst\Sentry\Checkpoints\ActivationCheckpoint;
use Cartalyst\Sentry\Checkpoints\SwiftIdentityCheckpoint;
use Cartalyst\Sentry\Checkpoints\ThrottleCheckpoint;
use Cartalyst\Sentry\Groups\IlluminateGroupRepository;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Persistence\SentryPersistence;
use Cartalyst\Sentry\Reminders\IlluminateReminderRepository;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Sessions\IlluminateSession;
use Cartalyst\Sentry\Swift\SentrySwift;
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
		$this->registerGroups();
		$this->registerCheckpoints();
		$this->registerReminders();
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
	}

	protected function registerUsers()
	{
		$this->registerHasher();

		$this->app['sentry.users'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::users.model'];

			$groups = $app['config']['cartalyst/sentry::groups.model'];
			if (class_exists($groups) and method_exists($groups, 'setUsersModel'))
			{
				forward_static_call_array(array($groups, 'setUsersModel'), array($model));
			}

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

	protected function registerGroups()
	{
		$this->app['sentry.groups'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::groups.model'];

			$users = $app['config']['cartalyst/sentry::users.model'];
			if (class_exists($users) and method_exists($users, 'setGroupsModel'))
			{
				forward_static_call_array(array($users, 'setGroupsModel'), array($model));
			}

			return new IlluminateGroupRepository($model);
		});
	}

	protected function registerCheckpoints()
	{
		$this->registerActivationCheckpoint();
		$this->registerSwiftCheckpoint();
		$this->registerThrottleCheckpoint();

		$this->app['sentry.checkpoints'] = $this->app->share(function($app)
		{
			$checkpoints = $app['config']['cartalyst/sentry::checkpoints'];

			$checkpoints = array_map(function($checkpoint) use ($app)
			{
				switch ($checkpoint)
				{
					case 'activation':
					case 'swift':
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
			return new ActivationCheckpoint($app['sentry.activations']);
		});
	}

	protected function registerActivation()
	{
		$this->app['sentry.activations'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::activations.model'];
			$expires = $app['config']['cartalyst/sentry::activations.expires'];

			return new IlluminateActivationRepository($model, $expires);
		});
	}

	protected function registerSwiftCheckpoint()
	{
		$this->registerSwift();

		$this->app['sentry.checkpoint.swift'] = $this->app->share(function($app)
		{
			return new SwiftIdentityCheckpoint($app['sentry.swift']);
		});
	}

	protected function registerSwift()
	{
		$this->app['sentry.swift'] = $this->app->share(function($app)
		{
			$email = $app['config']['cartalyst/sentry::swift.email'];
			$password = $app['config']['cartalyst/sentry::swift.password'];
			$apiKey = $app['config']['cartalyst/sentry::swift.api_key'];
			$appCode = $app['config']['cartalyst/sentry::swift.app_code'];
			$method = $app['config']['cartalyst/sentry::swift.method'];
			$model = $app['config']['cartalyst/sentry::swift.model'];

			return new SentrySwift(
				$email,
				$password,
				$apiKey,
				$appCode,
				$this->app['request']->getClientIp(),
				$method,
				$model
			);
		});
	}

	protected function registerThrottleCheckpoint()
	{
		$this->registerThrottling();

		$this->app['sentry.checkpoint.throttle'] = $this->app->share(function($app)
		{
			return new ThrottleCheckpoint(
				$app['sentry.throttling'],
				$app['request']->getClientIp()
			);
		});
	}

	protected function registerThrottling()
	{
		$this->app['sentry.throttling'] = $this->app->share(function($app)
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

	protected function registerReminders()
	{
		$this->app['sentry.reminders'] = $this->app->share(function($app)
		{
			$model = $app['config']['cartalyst/sentry::reminders.model'];
			$expires = $app['config']['cartalyst/sentry::reminders.expires'];

			return new IlluminateReminderRepository($app['sentry.users'], $model, $expires);
		});
	}

	protected function registerSentry()
	{
		$this->app['sentry'] = $this->app->share(function($app)
		{
			$sentry = new Sentry(
				$app['sentry.persistence'],
				$app['sentry.users'],
				$app['sentry.groups'],
				$app['events']
			);

			if (isset($app['sentry.checkpoints']))
			{
				foreach ($app['sentry.checkpoints'] as $checkpoint)
				{
					$sentry->addCheckpoint($checkpoint);
				}
			}

			$sentry->setActivationsRepository($app['sentry.activations']);
			$sentry->setRemindersRepository($app['sentry.reminders']);

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
			'sentry.groups',
			'sentry.activations',
			'sentry.checkpoint.activation',
			'sentry.swift',
			'sentry.checkpoint.swift',
			'sentry.throttling',
			'sentry.checkpoint.throttle',
			'sentry.checkpoints',
			'sentry.reminders',
			'sentry',
		);
	}

}
