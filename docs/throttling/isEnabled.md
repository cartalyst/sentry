<a id="isEnabled"></a>
###isEnabled()

----------

Checks to see the throttling feature is enabled, like `enable()` and `disable()`, this can be done globally or on an individual throttle instance.

`returns` bool

####Example

	try
	{
		$provider = Sentry::getThrottleProvider();
		$globallyEnabled = $provider->isEnabled();

		$throttle = $provider->findByUserId(1);
		$instanceEnabled = $throttle->isEnabled();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}