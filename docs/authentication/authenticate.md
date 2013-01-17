<a id="authenticate"></a>
###authenticate($credentials, $remember = false)

----------

Authenticates a user based on credentials. If successful, password reset fields and any invalid authentication attempts will be cleared.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$credentials`      | array               | none                | true                | An array of user fields to validate and login a user by. Both login and password fields are required, all other fields are optional.
`$remember`         | bool                | false               | false               | Remembers if the user is authenticated or not for auto logging in.

`returns` UserInterface
`throws`  LoginRequiredException, PasswordRequiredException, UserNotFoundException, UserNotActivatedException, UserSuspendedException, UserBannedException

####Example

	try
	{
		// Set login credentials
		$credentials = array(
			'email'    => 'testing@test.com',
			'password' => 'test'
		);

		// Try to authenticate the user
		if ($user = Sentry::authenticate($credentials))
		{
			// Passed authentication
		}
		else
		{
			// Failed authentication
		}
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field is required.';
	}
	catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
	{
		echo 'Password field is required.';
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		// Sometimes a user is found, however hashed credentials do
		// not match. Therefore a user technically doesn't exist
		// by those credentials. Check the error message returned
		// for more information.
		echo 'User not found.';
	}
	catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
	{
		echo 'User not activated.';
	}

	// The following is only required if throttle is enabled
	catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
	{
		echo 'User suspended.';
	}
	catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
	{
		echo 'User banned.';
	}
