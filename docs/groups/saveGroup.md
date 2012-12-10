<a id="saveGroup"></a>
###saveGroup($group)

----------

Delete a group object.  This can both create and delete an existing group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$group`                     | GroupInterface  | none          | An GroupInterface object to delete.

`returns` bool
`throws`  GroupNotFoundException

####Example

	try
	{
		// edit existing group
		$group = Sentry::group()->findById(1);

		// or create a new group
		$group = Sentry::group();
		$group->name = 'Users';
		$group->permissions = array(
			'admin' => 1,
			'users' => 1,
		);

		// save
		if (Sentry::saveGroup($group))
		{
			// group saved/created
		}

		// group not saved/created
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