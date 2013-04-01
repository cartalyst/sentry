#### Delete a User

The delete method deletes a user.

return `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Delete the user
		if ($user->delete())
		{
			// User was successfully deleted
		}
		else
		{
			// There was a problem deleting the user
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
