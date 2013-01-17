<a id="delete"></a>
###delete()

----------

Deletes a group object.

`returns` bool
`throws`  GroupNotFoundException

####Example

	try
	{
		// Find the group
		$group = Sentry::getGroupProvider()->findById(1);

		// Try to delete the group
		if ($group->delete())
		{
			// Group deleted
		}
		else
		{
			// Group not deleted
		}
	}
	catch (Cartalyst\Sentry\Grousp\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}
