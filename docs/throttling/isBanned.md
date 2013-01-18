<a id="isBanned"></a>
###isBanned()

----------

Checks to see if the user is banned.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$banned = $throttle->isBanned();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
