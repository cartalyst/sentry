<a id="add" href="#"></a>
###add()

----------

The add method adds an attempt to a certain login/ip combo.

`throws` Sentry\SentryException

####Example

	// add an attempt
	try
	{
	    Sentry::attempts('john.doe@domain.com', '123.432.2.1')->add(); // works fine

	    Sentry::attempts()->add(); // this or any other combo will throw an exception - login/ip required
	}
	catch (Sentry\SentryException $e)
	{
	    $error = $e->getMessage();
	}
