<a id="getSuspensionTime"></a>
###getSuspensionTime()

----------

Retrieves the length of the suspension time set by the throttling driver.

`returns` integer
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$attempts = $throttle->getSuspensionTime();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
