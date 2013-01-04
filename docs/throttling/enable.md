<a id="enable"></a>
###enable()

----------

Enables throttling feature. Can be done on the throttle provider (global) level or on a throttle instance itself.

####Example

	try
	{
		$provider = Sentry::getThrottleProvider();
		$provider->enable();

		$throttle = $provider->findByUserId(1);
		$throttle->enable();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}