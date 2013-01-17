<a id="attemptResetPassword"></a>
###attemptResetPassword($resetCode, $newPassword)

----------

Checks a reset code supplied by the user.  If the reset code is valid a new password will be given to the user.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$resetCode`        | string              | none                | true                | Reset Code
`$newPassword`      | string              | none                | true                | New Password

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);

		if ($user->attemptResetPassword('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb', 'new_password'))
		{
			echo 'Password reset.';
		}
		else
		{
			echo 'Password not reset.';
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
