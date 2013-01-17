<a id="suspend"></a>
###suspend()

----------

Suspends a user temporarily. Length of the suspension is set by the driver or setSuspensionTime($minutes).

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);

		// Suspend the user
		$throttle->suspend();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
