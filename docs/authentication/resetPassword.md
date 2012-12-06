<a id="resetPassword"></a>
###resetPassword

----------

Resets a user's password. This will generate a random string to send the user, which they will use to validate their password reset request.

`returns` string

####Example

	try
	{
		$resetCode = Sentry::user('test@test.com')->resetPassword();

		// email the reset code to the user
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}