#### Disable a User

Disables a user.

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Disable the user
		if ($user->disable())
		{
			// User was successfully disabled
		}
		else
		{
			// There was a problem disabling the user
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
