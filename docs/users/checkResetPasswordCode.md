<a id="checkResetPassword"></a>
###checkResetPasswordCode()

----------

Checks if the provided reset password code is valid.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------------
`$resetCode`                 | string          | none          | Reset Password Hash

`returns` bool

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);

		if ($user->checkResetPasswordCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
		{
			// The provided Reset Password Code is Valid
		}
		else
		{
			// The provided Reset Password Code is Invalid
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
