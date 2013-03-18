<a id="disable" href="#"></a>
###disable()

----------

Disables a user.

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    $disabled = Sentry::user(25)->disable();
	    if ($disabled)
	    {
	        // user was disabled
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
