#### Change a User Password

The change_password method updates the users password. Their old password is required as well.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$password`                  | string          | null            | The users new password
`$old_password`              | string          | null            | The users old password

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Change the user password
		if ($user->change_password('newpassword', 'oldpassword'))
		{
			// User password was successfully updated
		}
		else
		{
			// There was a problem updating the user password
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
