<a id="saveGroup"></a>
###saveGroup($group)

----------

Delete a group object.  This can be used to update an existing group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------

`returns` bool
`throws`  GroupNotFoundException

####Example

	try
	{
		// Grab a group
		$group = Sentry::getGroupProvider()->findById(1);

		$group->name = 'Users';
		$group->permissions = array(
			'admin' => 1,
			'users' => 1,
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
	catch (Cartalyst\Sentry\Groups\NameFieldRequiredException $e)
	{
		echo 'Name field required.';
	}
	catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
	{
		echo 'Group already exists.';
	}