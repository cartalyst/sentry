<a id="unsuspend"></a>
###unsuspend()

----------

Unsuspends a login. This also clears all previous attempts by the specified login if they were suspended.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);

		// Un-suspend the user
		$throttle->suspend();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
