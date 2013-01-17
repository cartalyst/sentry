<a id="clearResetPassword"></a>
###clearResetPassword()

----------

Clears the password reset field. This automatically gets called whenever a user logs in via `authenticate()` or `authenticateAndRemember()`.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Clear the password reset code
		$user->clearResetPassword();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
