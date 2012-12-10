<a id="loginAndRemember"></a>
###loginAndRemember($user)

----------

Log a user in and remember them. This is a helper function for login() which autosets the $remember ( 2nd param ) to true so the user is remembered.
Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$user` (required)           | UserInterface   | none          | UserInterface Object to log in with.
`$checkThrottle`             | bool            | true          | Check the user throttle status.

`returns` bool
`throws`  UserNotFoundException, UserNotActivatedException, UserSuspendedExceptions, UserBannedException

####Example

	// select a user
	try
	{
		$user = Sentry::user()->findById(1);

		Sentry::loginAndRemember($user)
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found';
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
