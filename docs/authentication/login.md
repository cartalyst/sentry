<a id="login"></a>
###login($user, $remember = false)

----------

Logs a user in.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$user`             | UserInterface       | none                | true                | UserInterface Object to log in with.
`$remember`         | bool                | false               | false               | Remembers if the user is authenticated or not for auto logging in.

`returns` bool
`throws`  LoginRequiredException, UserNotFoundException, UserNotActivatedException, UserSuspendedExceptions, UserBannedException

####Example

	try
	{
		// Select a user
		$user = Sentry::getUserProvider()->findById(1);

		// Log the user in
		Sentry::login($user);
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field is required.';
	}
	catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
	{
		echo 'User not activated.';
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
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
