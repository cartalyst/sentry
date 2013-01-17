<a id="isActivated"></a>
###isActivated()

----------

Checks if a user is activated.

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);

		if ($user->isActivated())
		{
			echo 'Activated.';
		}
		else
		{
			echo 'Not activated.';
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
