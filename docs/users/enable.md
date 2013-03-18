<a id="enable" href="#"></a>
###enable()

----------

Enables a user.

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    $enabled = Sentry::user(25)->enable();
	    if ($enabled)
	    {
	        // user was enabled
	    }
	    else
	    {
	        // something went wrong
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
