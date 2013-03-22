#### Force a User to Log In

Forces a login. A user will be logged in as long as that user exists.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$id`                        | int, string     | null            | The user id or loing ( email or username ).
`$provider`                  | string          | Sentry-Forced   | What system was used to force the login

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Force login
		Sentry::force_login('john.doe@example.com');
	}
	catch (Sentry\SentryException $e)
	{
		$error = $e->getMessage();
	}
