<a id="getResetPasswordCode"></a>
###getResetPasswordCode()

----------

Resets a user's password. This will generate a random string to send the user, which they will use to validate their password reset request.

`returns` string
`throws`  UserNotFoundException

####Example

	try
	{
		$user      = Sentry::getUserProvider()->findByLogin('test@test.com');
		$resetCode = $user->getResetPasswordCode();

		// Email the reset code to the user
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist/';
	}
