<a id="check" href="#"></a>
###check()

----------

The check methods returns a bool of whether the user is logged in or not.

`returns` bool

####Example

	// basic login check example
	if (Sentry::check())
	{
	    // the user is logged in
	}
	else
	{
	    // the user is not logged in
	}

	// user needs to be logged in to view page example
	if ( ! Sentry::check())
	{
	    // redirect to login
	}
