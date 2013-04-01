#### Suspend a User

The suspend method suspends a login/ip combo for a set amount of time.

throws `Sentry\SentryException`

##### Example

	try
	{
		// Suspend the user using the email and ip address
		Sentry::attempts('john.doe@example.com', '123.432.2.1')->suspend();
	}
	catch (Sentry\SentryException $e)
	{
		$error = $e->getMessage();
	}
