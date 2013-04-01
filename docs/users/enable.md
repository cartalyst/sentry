##### Enable a User

Enables a user.

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Enable the user
		if ($user->enable())
		{
			// User was successfully enabled
		}
		else
		{
			// There was a problem enabling the user
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
