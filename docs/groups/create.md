<a id="create"></a>
###create($attributes)

----------

Creates a group.

Parameters                   | Type           | Default       | Description
:--------------------------- | :------------- | :------------ | :--------------
`$credentials`               | array          | none          | An array of group fields create a group with. The Name field is required, all other fields are optional.

`returns` GroupInterface
`throws`  NameFieldRequiredException, GroupExistsException, InvalidPermissionsException

####Example

	try
	{
		$group = Sentry::getGroupProvider()->create(array(
			'name'    => 'Users',
			'permissions' => array(
				'admin' => 1,
				'users' => 1,
			)
		));
	}
	catch (Cartalyst\Sentry\Groups\NameFieldRequiredException $e)
	{
		echo 'Name field required';
	}
	catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
	{
		echo 'Group already exists';
	}
