<a id="getUserId"></a>
###getUserId()

----------

Returns the user's ID.

`returns` int
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Get the user id
		$userId = $user->getUserId();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		// User wasn't found, should only happen if the user was deleted
		// when they were already logged in or had a "remember me" cookie set
		// and they were deleted.
	}
