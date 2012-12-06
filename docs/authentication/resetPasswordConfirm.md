<a id="resetPasswordConfirm"></a>
###resetPasswordConfirm

----------

Checks a reset code supplied by the user.  If the reset code is valid a new password will be given to the user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$password`                  | string          | none          | New Password
`$activationCode`            | string          | none          | Activation Code

`returns` bool

####Example

	try
	{
		if ( Sentry::user('test@test.com')->resetPasswordConfirm('newpass', '8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb') )
		{
			echo 'password reset';
		}
		else
		{
			echo 'invalid reset code';
		}

		// email the reset code to the user
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}