<a id="create"></a>
###create($attributes)

----------

Creates a user.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$attributes`       | array               | none                | true                | An array of user fields to create a user with. The Login field is required, all other fields are optional.

`returns` UserInterface
`throws`  LoginRequiredException, UserExistsException, InvalidPermissionsException

####Example

	try
	{
		$user = Sentry::getUserProvider()->create(array(
			'email'    => 'testing@test.com',
			'password' => 'test',
			'permissions' => array(
				'test'  => 1,
				'other' => -1,
				'admin' => 1
			)
		));
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field required.';
	}
	catch (Cartalyst\Sentry\Users\UserExistsException $e)
	{
		echo 'User with login already exists.';
	}
