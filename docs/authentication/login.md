<a id="login"></a>
###login($user, $remember = false)

----------

Log a user in.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$user` (required)           | UserInterface   | none          | UserInterface Object to log in with.
`$remember`                  | bool            | false         | Remember if the user is authenticated or not for auto logging in.
`$checkThrottle`             | bool            | true          | Check the user throttle status.

`returns` bool
`throws`  UserNotFoundException, UserNotActivatedException, UserSuspendedExceptions, UserBannedException

####Example

	// select a user
	try
	{
		$user = Sentry::getUserProvider()->findById(1);

		Sentry::login($user)
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found';
	}
	catch (Cartalyst\Sentry\LoginRequiredException $e)
	{
		echo 'Login Field required';
	}
	catch (Cartalyst\Sentry\UserNotActivatedException $e)
	{
		echo 'User not Activated';
	}
	// following is only needed if throttle is enabled
	catch (Cartalyst\Sentry\UserSuspendedException $e)
	{
		echo 'User Suspended';
	}
	catch (Cartalyst\Sentry\UserBannedException $e)
	{
		echo 'User Banned';
	}