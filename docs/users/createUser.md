<a id="createUser"></a>
###createUser($attributes)

----------

Creates a user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials`               | array           | none          | An array of user fields create a user with. The Login field is required, all other fields are optional.

`returns` bool
`throws`  LoginRequiredException, UserExistsException, InvalidPermissionsException

####Example

	try
	{
		echo Sentry::createUser(array(
			'email'    => 'testing@test.com',
			'password' => 'test',
			'permissions' => array(
				'test' => 1,
				'other' => -1,
				'admin' => 1,
			)
		));
	}
	catch (Cartalyst\Sentry\LoginRequiredException $e)
	{
		echo 'login field required';
	}
	catch (Cartalyst\Sentry\UserExistsException $e)
	{
		echo 'login already exists';
	}
	// only thrown if setting permissions
	catch (Cartalyst\Sentry\InvalidPermissionException $e)
	{
		echo 'Invalid Permission Value';
	}