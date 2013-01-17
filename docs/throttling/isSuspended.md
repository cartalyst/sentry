<a id="isSuspended"></a>
###isSuspended()

----------

Checks to see if the user is suspended.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$suspended = $throttle->isSuspended();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
