<a id="authenticate"></a>
###authenticate($credentials, $remember = false)

----------

Authenticate a user based on credentials. If successful, password reset fields and any invalid authentication attempts will be cleared.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials` (required)    | array           | none          | An array of user fields to validate and login a user by. The Login field is required, all other fields are optional.
`$remember`                  | bool            | false         | Remember if the user is authenticated or not for auto logging in.

`returns` UserInterface
`throws`  LoginRequiredException, UserNotFoundException, UserNotActivatedException, UserSuspendedException, UserBannedException

####Example

	try
	{
		// Set login credentials
		$credentials = array(
			'email'    => 'testing@test.com',
			'password' => 'test',
		);

		if ($user = Sentry::authenticate($credentials))
		{
			echo 'Authenticated';

			// Because the user activated, you can also access it through
			// Sentry::getUser();. Additionally, any methods available on
			// the Cartalyst\Sentry\Users\UserInterface are now available
			// through the Sentry object. This means you can achieve the same
			// thing three ways:
			$groups = Sentry::getUserProvider()->findByLogin('testing@test.com')->getGroups();
			$user->getGroups(); // If you have the $user object
			Sentry::getGroups(); // Available when the $user property of Sentry is set.
		}
		else
		{
			echo 'Failed authentication.';
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
		// Sometimes a user is found however hashed credentials do
		// not match. Therefore a user technically doesn't exist
		// by these credentials. Check the error message returned
		// for more information
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