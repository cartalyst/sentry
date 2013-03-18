<a id="all" href="#"></a>
###all()

----------

The all method returns all groups

`returns` array `throws` Sentry\SentryException

####Example

	// get group information
	try
	{
	    $groups = Sentry::group()->all();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
