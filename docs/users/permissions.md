<a id="permissions" href="#"></a>
###permissions()

----------

Retrieves the users permissions

`returns` array

####Example

	try
	{
		// get current users permissions
		$permissions = Sentry::user()->permissions();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user does not exist.
	}
