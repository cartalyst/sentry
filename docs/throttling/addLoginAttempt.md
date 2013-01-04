<a id="addLoginAttempt"></a>
###addLoginAttempt()

----------

Add an attempt to the throttle object

`returns` void

####Example
	
	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->addAttempt();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}