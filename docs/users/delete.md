<a id="delete"></a>
###delete()

----------

Delete a user object.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// Find existing user
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