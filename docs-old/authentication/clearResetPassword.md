<a id="clearResetPassword"></a>
###clearResetPassword()

----------

Clears the password reset field. This automatically gets called whenever a user logs in via authenticate() or authenticateAndRemember();

`returns` bool

####Example

	try
	{
		Sentry::user('test@test.com')->clearResetPassword();

		// email the reset code to the user
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}