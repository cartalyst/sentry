<a id="delete"></a>
###delete()

----------

Delete a user object.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Delete
		if ($user->delete())
		{
			// User deleted
		}
		else
		{
			// User not deleted
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
	}
