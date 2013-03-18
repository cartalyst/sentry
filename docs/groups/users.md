<a id="users" href="#"></a>
###users()

----------

The users method returns all the users in the group.

`returns` array `throws` Sentry\SentryException

####Example

	// get group information
	try
	{
	    $users = Sentry::group(2)->users();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
