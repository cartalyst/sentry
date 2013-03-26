#### Add Attempt

The add method adds an attempt to a certain login/ip combo.

throws `Sentry\SentryException`

##### Example

	try
	{
		// Add an attempt using the email and ip address
		Sentry::attempts('john.doe@example.com', '123.432.2.1')->add();
	}
	catch (Sentry\SentryException $e)
	{
		$error = $e->getMessage();
	}
