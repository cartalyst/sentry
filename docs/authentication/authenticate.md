<a id="authenticate"></a>
###Authenticate

----------

Authenticate a user based on credentials. If successful, password reset fields and any invalid authentication attempts will be cleared.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials` (required)    | array           | none          | An array of user fields to validate and login a user by. The Login field is required, all other fields are optional.
`$remember`                  | bool            | false         | Remember if the user is authenticated or not for auto logging in.

`returns` bool
`throws`  LoginFieldRequiredException, UserNotFoundException, UserNotActivatedException, UserSuspendedException, UserBannedException

####Example

	try
	{
		// set login credentials
		$credentials = array(
			'email'    => 'testing@test.com',
			'password' => 'test',
		);

		if (Sentry::authenticate($credentials))
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
	catch (Cartalyst\Sentry\LoginFieldRequiredException $e)
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