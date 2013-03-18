<a id="update-permissions" href="#"></a>
###update_permissions($rules = array())

----------

Updates a groups permissions.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$rules`                     | array           |               | array of rules and there value ( 0 or 1 )

`returns` bool `throws` Sentry\SentryException

> **Note:** Permissions can also be updated through the `update()` method with a key of `permissions`

####Example

	try
	{
	    // update current user permissions
	    $permissions = array(
	    	'is_admin'   => 1 // add is_admin,
	    	'can_edit'   => 1 // add can_edit,
	    	'can_delete' => 0 // remove can_delete
	    );

		if (Sentry::group('admin')->update_permissions($permissions))
		{
		    // permissions were updated
		}
		else
		{
		    // permissions were not updated
		}
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors
	}
