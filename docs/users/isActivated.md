<a id="isActivated"></a>
###isActivated()

----------

Checks if a user is activated.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Check if the user is activated or not
		if ($user->isActivated())
		{
			// User is Activated
		}
		else
		{
			// User is Not Activated
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
