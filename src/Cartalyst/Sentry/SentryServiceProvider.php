<?php namespace Cartalyst\Sentry;

use Illuminate\Support\ServiceProvider;

class SentryServiceProvider extends ServiceProvider
{
	public function boot()
	{

	}

	public function register()
	{
		$session = $this->app['session'];
		$cookie  = $this->app['cookie'];

		$this->app['sentry'] = $this->app->share(function($app) use($session, $cookie)
		{
			return new Sentry(
				new Provider\Eloquent,
				new Session\Laravel($session),
				new Cookie\Laravel($cookie)
			);
		});
	}
}