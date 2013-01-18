<a id="getUserLogin"></a>
###getUserLogin()

----------

Returns the user's login.

`returns` string
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Get the user login
		$userLogin = $user->getUserLogin();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		// User wasn't found, should only happen if the user was deleted
		// when they were already logged in or had a "remember me" cookie set
		// and they were deleted.
	}
