#### Update User Permissions

Updates a users permissions.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$rules`                     | array           | null            | array of rules and there value ( 0 or 1 )

returns `bool`

throws `Sentry\SentryException`

> **Note:** Permissions can also be updated through the `update()` method with a key of `permissions`

##### Example

	try
	{
		// Prepare the user permissions
		$permissions = array(
			'is_admin'   => 1, // add is_admin,
			'can_edit'   => 1, // add can_edit,
			'can_delete' => 0, // remove can_delete
		);

		// Update current user permissions
		if (Sentry::user()->update_permissions($permissions))
		{
			// User permissions were successfully updated
		}
		else
		{
			// There was a problem updating the user permissions
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
