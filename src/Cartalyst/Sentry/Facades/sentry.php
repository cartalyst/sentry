<?php namespace Cartalyst\Sentry\Facades;

use Illuminate\Support\Facade;

class Sentry extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'sentry';
	}

}