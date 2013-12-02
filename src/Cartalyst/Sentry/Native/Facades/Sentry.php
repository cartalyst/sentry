<?php namespace Cartalyst\Sentry\Native\Facades;

use Cartalyst\Sentry\Native\SentryBootstrapper;

class Sentry {

	protected $sentry;

	protected static $instance;

	public function __construct(SentryBootstrapper $bootstrapper = null)
	{
		if ($bootstrapper === null)
		{
			$bootstrapper = new SentryBootstrapper;
		}

		$this->sentry = $bootstrapper->createSentry();
	}

	public function getSentry()
	{
		return $this->sentry;
	}

	public static function instance(SentryBootstrapper $bootstrapper = null)
	{
		if (static::$instance === null)
		{
			static::$instance = new static($bootstrapper);
		}

		return static::$instance;
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::instance()->getSentry();

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

}
