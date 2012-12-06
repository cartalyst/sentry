<a id="registerUser"></a>
###registerUser

----------

Registers a user which requires activation.  If the user already exists but is not activated, it will create a new activation code.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials`               | array           | none          | An array of user fields create a user with. The Login field is required, all other fields are optional.

`returns` bool
`throws`  LoginFieldRequiredException, UserExistsException, InvalidPermissionsException

####Example

	try
	{
		$activationCode = Sentry::registerUser(array(
			'email'    => 'testing@test.com',
			'password' => 'test'
		));

		// send activation code to user to activate their account
	}
	catch (Cartalyst\Sentry\LoginFieldRequiredException $e)
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