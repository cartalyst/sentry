<a id="deleteGroup"></a>
###deleteGroup($group)

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
		// find existing group
		$group = Sentry::getGroupProvider()->findById(1);

		// delete
		if (Sentry::deleteGroup($group))
		{
			// group deleted
		}

		// group not deleted
	}
	catch (Cartalyst\Sentry\groupNotFoundException $e)
	{
		echo 'group not found.';
	}