<a id="activeUser"></a>
###getUser()

----------

Returns the active logged in user or null.

`returns` UserInterface|null
`throws`  UserNotFoundException

####Example

	// get the current active/logged in user
	try
	{
		$user = Sentry::getUser();
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		// user wasn't found, should only happen if the user was deleted
		// when they were already logged in or had a remember cookie set
		// and they were deleted.
	}