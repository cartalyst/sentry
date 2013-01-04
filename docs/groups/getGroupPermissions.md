<a id="getGroupPermissions"></a>
###getGroupPermissions()

----------

Gets the group's permissions

`returns` array

####Example

	try
	{
		$group = Sentry::getGroupProvider()->findById(1);
		$groupPermissions = $group->getGroupPermissions();
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group does not exist.';
	}