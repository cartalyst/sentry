<a id="getAttempts"></a>
###getAttempts()

----------

Retrieves the number of attempts a user currently has tried. Checks suspension time to see if login attempts can be reset. This may happen if the suspension time was (for example) 10 minutes however the last login was 15 minutes ago - attempts will be reset to 0.

`returns` int
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$attempts = $throttle->getAttemps();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
