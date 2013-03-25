#### Reset a User Password

The reset_password method returns an array or false. The new password and reset hash will be stored in temporary fields in the database.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column_value`        | string          | null            | The users login ( email or username ).
`$password`                  | string          | true            | A new password that the user wants to use.

returns `bool` or `array`

throw `Sentry\SentryException`

> **Notes:** The new password will not come into effect until confirmed. Also, if a user logs into the account before the password is confirmed, the entire reset password process will be nullified

##### Example

	try
	{
		// Reset the password
		if ($reset = Sentry::reset_password('john.doe@example.com', 'newpassword'))
		{
			$email = $reset['email'];
			$link = 'domain.com/auth/reset_password_confirm/'.$reset['link']; // adjust path as needed

			// Send the activation $link through email
		}
		else
		{
			// There was a problem resetting the user password
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
