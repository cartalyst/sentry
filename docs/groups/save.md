<a id="save"></a>
###save()

----------

Saves a group object.

`returns` bool
`throws`  NameRequiredException, GroupExistsException, GroupNotFoundException

####Example

	try
	{
		// Find the group
		$group = Sentry::getGroupProvider()->findById(1);

		$group->name = 'Users';
		$group->permissions = array(
			'admin' => 1,
			'users' => 1
		);

		// Save
		if ($group->save())
		{
			// Group saved
		}
		else
		{
			// Group not saved
		}
	}
	catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
	{
		echo 'Name field required.';
	}
	catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
	{
		echo 'Group already exists.';
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}
