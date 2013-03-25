#### Activate a User

The activate_user method activates a non-active user.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column_value`        | string          | null            | The users login ( email or username ).
`$code`                      | string          | null            | The users activation hash.
`$decode`                    | bool            | true            | If the login value needs to be decoded.

returns `bool` or `array`

throws `Sentry\SentryException`

###### Example

	try
	{
		// Activate the user
		$activate_user = Sentry::activate_user('VGhpcyBpcyBhbiBlbmNvZG...', '93kavFY63S8jtala93a76fQ...');

		or

		// Activate the user using a non hashed login value
		$activate_user = Sentry::activate_user('john.doe@example.com', '93kavFY63S8jtala93a76fQ...', false);


		if ($activate_user)
		{
			// User was successfully activated
		}
		else
		{
			// There was a problem activating the user
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
