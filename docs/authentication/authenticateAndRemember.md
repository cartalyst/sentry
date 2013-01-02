<a id="authenticateAndRemember"></a>
###authenticateAndRemember($credentials)

----------

Authenticate and Remember a user based on credentials. This is a helper function for authenticate() which autosets the $remember ( 2nd param ) to true so the user is remembered.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials` (required)    | array           | none          | An array of user fields to validate and login a user by. The Login field is required, all other fields are optional.

`returns` bool
`throws`  LoginRequiredException, UserNotFoundException, UserNotActivatedException, UserSuspendedExceptions, UserBannedException

####Example

	try
	{
		// set login credentials
		$credentials = array(
			'email'    => 'testing@test.com',
			'password' => 'test',
		);

		if (Sentry::authenticateAndRemember($credentials))
		{
			echo 'authenticated';
		}
		else
		{
			echo 'failed authentication';
		}
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
	// the following is only required if throttle is enabled
	catch (Cartalyst\Sentry\UserSuspendedException $e)
	{
		echo 'User Suspended';
	}
	catch (Cartalyst\Sentry\UserBannedException $e)
	{
		echo 'User Banned';
	}