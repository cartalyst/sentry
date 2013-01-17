<a id="getAttemptLimit"></a>
###getAttemptLimit()

----------

Retrieves the number of attempts allowed by the throttle object.

`returns` int
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$attemptLimit = $throttle->getAttemptLimit();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
