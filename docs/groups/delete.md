<a id="delete"></a>
###delete()

----------

Delete a group object.  This can both create and delete an existing group.

`returns` bool
`throws`  GroupNotFoundException

####Example

	try
	{
		// Find existing group
		$group = Sentry::getGroupProvider()->findById(1);

		// Delete
		if ($group->delete())
		{
			// group deleted
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