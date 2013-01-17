<a id="login"></a>
###login($user, $remember = false)

----------

Logs a user in.

Parameters                   | Type           | Default       | Description
:--------------------------- | :------------- | :------------ | :--------------
`$user` (required)           | UserInterface  | none          | UserInterface Object to log in with.
`$remember`                  | boolean        | false         | Remember if the user is authenticated or not for auto logging in.

`returns` boolean
`throws`  UserNotFoundException, UserNotActivatedException, UserSuspendedExceptions, UserBannedException

####Example

	try
	{
		// Select a user
		$user = Sentry::getUserProvider()->findById(1);

		// Log the user in
		Sentry::login($user);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field is required.';
	}
	catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
	{
		echo 'User not activated.';
	}

	// Following is only needed if throttle is enabled
	catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
	{
		echo 'User suspended.';
	}
	catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
	{
		echo 'User banned.';
	}
