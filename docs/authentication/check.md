<a id="check"></a>
###check()

----------

The check method returns a bool of whether the user is logged in or not, or if the user is not activated. If it's logged in, the current User is set in Sentry so you can easily access it via `getUser()`.

A user must be activated to pass `check()`.

`returns` bool

####Example

	if ( ! Sentry::check())
	{
		// User is not logged in, or is not activated
	}
	else
	{
		// User is logged in
	}
