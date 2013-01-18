<a id="getGroupPermissions"></a>
###getGroupPermissions()

----------

Gets the group's permissions

`returns` array

####Example

	try
	{
		// Find the group
		$group = Sentry::getGroupProvider()->findById(1);

		// Get the group permissions
		$groupPermissions = $group->getGroupPermissions();
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group does not exist.';
	}
