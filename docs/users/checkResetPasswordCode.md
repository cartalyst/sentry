<a id="checkResetPassword"></a>
###checkResetPasswordCode()

----------

Checks if the provided password reset code is valid.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$resetCode`        | string              | none                | true                | Reset Password Hash

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Check if the provided password reset code is valid
		if ($user->checkResetPasswordCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
		{
			// The provided password reset code is Valid
		}
		else
		{
			// The provided password reset code is Invalid
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
