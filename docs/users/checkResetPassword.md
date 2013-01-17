<a id="checkResetPassword"></a>
###checkResetPassword()

----------

Checks if the provided reset password hash is valid.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------------
`$resetPasswordHash`         | string          | none          | Reset Password Hash

`returns` bool

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);

		if ($user->checkResetPassword('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
		{
			// The provided Reset Password Hash is Valid
		}
		else
		{
			// The provided Reset Password Hash is Invalid
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
