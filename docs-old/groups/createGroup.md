<a id="createGroup"></a>
###createGroup($attributes)

----------

Creates a user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials`               | array           | none          | An array of group fields create a group with. The Name field is required, all other fields are optional.

`returns` bool
`throws`  NameFieldRequiredException, GroupExistsException, InvalidPermissionsException

####Example

	try
	{
		echo Sentry::createGroup(array(
			'name'    => 'Users',
			'permissions' => array(
				'admin' => 1,
				'users' => 1,
			)
		));
	}
	catch (Cartalyst\Sentry\NameFieldRequiredException $e)
	{
		echo 'Name field required';
	}
	catch (Cartalyst\Sentry\GroupExistsException $e)
	{
		echo 'Group already exists';
	}
	// only thrown if setting permissions
	catch (Cartalyst\Sentry\InvalidPermissionException $e)
	{
		echo 'Invalid Permission Value';
	}