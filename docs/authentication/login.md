#### Logs a User In

The login method logs the user in.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column_value`        | string          | null            | The users login (email or username).
`$password`                  | string          | null            | The users password.
`$remember`                  | bool            | false           | Whether the remember me cookie should be created or not.

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		if (Sentry::login('john.doe@example.com', 'secretpassword', true))
		{
			// The user is now logged in
		}
		else
		{
			// Could not log the user in
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
