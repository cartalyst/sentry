<a id="check"></a>
###check()

----------

Checks the login throttle status and throws a number of Exceptions upon failure.

`returns` bool
`throws`  UserNotFoundException, UserBannedException, UserSuspendedException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);

		if ($throttle->check())
		{
			echo 'Good to go.';
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
	catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
	{
		ehco 'User is banned.';
	}
	catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
	{
		$time = $throttle->getSuspensionTime();

		echo "User is suspended for [$time] minutes.";
	}
