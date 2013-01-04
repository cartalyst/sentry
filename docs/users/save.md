<a id="save"></a>
###save()

----------

Saves a user object.

`returns` bool
`throws`  LoginRequiredException, UserExistsException

####Example

	try
	{
		// Grab a user
		$user = Sentry::getUserProvider()->findById(1);

		$user->email = 'test@test.com';
		$user->first_name = 'John';

		if ($user->save())
		{
			// User saved
		}
		else
		{
			// User not saved
		}
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field required.';
	}
	catch (Cartalyst\Sentry\Users\UserExistsException $e)
	{
		echo 'User already exists.';
	}