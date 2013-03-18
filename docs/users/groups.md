<a id="groups" href="#"></a>
###groups()

----------

Returns an array of groups the user is part of.

`returns` array()

####Example

	try
	{
	    // find the user
	    $user = Sentry::user(25);
	    $user_groups = $user->groups();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user doesn't exist
	}
