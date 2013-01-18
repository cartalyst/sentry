<a id="clearLoginAttempts"></a>
###clearLoginAttempts()

----------

Clears all login attempts, it also unsuspends them. This does not unban a login.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->clearLoginAttempts();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
