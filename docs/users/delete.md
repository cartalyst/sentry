<a id="delete" href="#"></a>
###delete()

----------

The delete method deletes a user.

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    // update the user
	    $user = Sentry::user(25);
	    $delete = $user->delete();

	    if ($delete)
	    {
	        // the user was deleted
	    }
	    else
	    {
	        // something went wrong
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user not existing
	}
