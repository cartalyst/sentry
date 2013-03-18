<a id="suspend" href="#"></a>
###suspend()

----------

The suspend method suspends a login/ip combo for a set amount of time.

`throws` Sentry\SentryException

####Example

	// suspend a user
	try
	{
	    Sentry::attempts('john.doe@domain.com', '123.432.2.1')->suspend(); // works fine

	    Sentry::attempts()->suspend(); // this or any other combo will throw an exception - login/ip required
	}
	catch (Sentry\SentryException $e)
	{
	    $error = $e->getMessage();
	}
