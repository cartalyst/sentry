<a id="saveUser"></a>
###saveUser($user)

----------

Saves a user object.  This can both create and save an existing user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$user`                      | UserInterface   | none          | An UserInterface object to save.

`returns` bool
`throws`  LoginRequiredException, UserExistsException, InvalidPermissionsException

####Example

	try
	{
		// edit existing user
		$user = Sentry::user()->findById(1);

		// or create a new user
		$user = Sentry::user();
		$user->email = 'test@test.com';
		$user->first_name = 'John';

		if (Sentry::saveUser($user))
		{
			// user saved
		}

		// user not saved
	}
	catch (Cartalyst\Sentry\LoginRequiredException $e)
	{
		echo 'login field required';
	}
	catch (Cartalyst\Sentry\UserExistsException $e)
	{
		echo 'User already exists';
	}
	// only thrown if setting permissions
	catch (Cartalyst\Sentry\InvalidPermissionException $e)
	{
		echo 'Invalid Permission Value';
	}