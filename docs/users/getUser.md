<a id="getUser"></a>
###getUser()

----------

Returns the user that's set with Sentry, does not check if a user is logged in or not. To do that, use `check()` instead.

`returns` UserInterface|null
`throws`  UserNotFoundException

####Example

	try
	{
		// Get the current active/logged in user
		$user = Sentry::getUser();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		// User wasn't found, should only happen if the user was deleted
		// when they were already logged in or had a "remember me" cookie set
		// and they were deleted.
	}
