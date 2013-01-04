<a id="check"></a>
###check()

----------

The check methods returns a bool of whether the user is logged in or not. If it's logged in, the current User is set in Sentry so you can access it easily via `getUser()`.

A user must be activated to pass `check()`.

`returns` bool

####Example

	if ( ! Sentry::check())
	{
		// No user is logged in (or activated), redirect or do whatever you want
	}
	else
	{
		// User is logged in
	}