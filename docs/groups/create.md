<a id="create"></a>
###create($attributes)

----------

Creates a new group.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$attributes`       | array               | none                | true                | An array of group fields to create a group with. The Name field is required, all other fields are optional.

`returns` GroupInterface
`throws`  NameRequiredException, GroupExistsException

####Example

	try
	{
		$group = Sentry::getGroupProvider()->create(array(
			'name'        => 'Users',
			'permissions' => array(
				'admin' => 1,
				'users' => 1
			)
		));
	}
	catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
	{
		echo 'Name field required';
	}
	catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
	{
		echo 'Group already exists';
	}
