<a id="clearResetPassword"></a>
###clearResetPassword()

----------

Clears the password reset field. This automatically gets called whenever a user logs in via `authenticate()` or `authenticateAndRemember()`.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);
		$user->clearResetPassword();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
