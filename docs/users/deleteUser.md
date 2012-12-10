<a id="deleteUser"></a>
###deleteUser($user)

----------

Delete a user object.  This can both create and delete an existing user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$user`                      | UserInterface   | none          | An UserInterface object to delete.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// find existing user
		$user = Sentry::user()->findById(1);

		// delete
		if (Sentry::deleteUser($user))
		{
			// user deleted
		}

		// user not deleted
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found.';
	}