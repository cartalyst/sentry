<a id="clearAttempts"></a>
###clearAttempts()

----------

Clears all a logins attempts as well as unsuspending them. This does not unban a login.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->clearAttempts();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
