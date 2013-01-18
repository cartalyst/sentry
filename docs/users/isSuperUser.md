<a id="isSuperUser"></a>
###isSuperUser()

----------

Returns if the user is a super user, it means, that has access to everything regardless of permissions.

`returns` bool

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Check if the user is a super user
		if ($user->isSuperUser())
		{
			// User is a super user
		}
		else
		{
			// User is not a super user
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
