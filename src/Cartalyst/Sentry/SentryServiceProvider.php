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