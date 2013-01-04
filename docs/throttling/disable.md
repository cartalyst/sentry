<a id="disable"></a>
###disable()

----------

Disables throttling feature. Can be done on the throttle provider (global) level or on a throttle instance itself.

####Example

	try
	{
		$provider = Sentry::getThrottleProvider();
		$provider->disable();

		$throttle = $provider->findByUserId(1);
		$throttle->disable();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}