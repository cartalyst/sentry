<a id="check"></a>
###check()

----------

The check methods returns a bool of whether the user is logged in or not.

`returns` bool

####Example

	if ( ! Sentry::check())
	{
		// no user is logged in, redirect or do whatever you want
	}

	// otherwise user is logged in