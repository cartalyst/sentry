<a id="addLoginAttempt"></a>
###addLoginAttempt()

----------

Adds an attempt to the throttle object.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->addLoginAttempt();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
