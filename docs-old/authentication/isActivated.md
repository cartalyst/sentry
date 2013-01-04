<a id="isActivated"></a>
###isActivated()

----------

Checks if a user is activated.

`returns` bool

####Example

	try
	{
		if (Sentry::getUserProvider()->findByLogin('test@test.com')->isActivated())
		{
			echo 'activated';
		}
		else
		{
			echo 'not activated';
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}