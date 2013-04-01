#### Check the User Password

The check_password method checks to make sure a given password matches the password stored in the database.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$password`                  | string          | null            | The password to check
`$field`                     | string          | password        | The column to check against

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Check if the provided password matches
		// the current password.
		if ($user->check_password('mypassword'))
		{
			// The provided password matches
		}
		else
		{
			// No, the provided password doesn't match
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
