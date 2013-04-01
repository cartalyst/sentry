#### Update a Group

The update method updates the current group.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$fields`                    | array           | null            | Array of fields to update for the group.

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the group using the group id
		$group = Sentry::group(4);

		// Update the group details
		$update = $group->update(array(
			'name'        => 'New Name',
			'permissions' => array(
				'is_admin' => 1,
			),
		));

		if ($update)
		{
			// Group was updated
		}
		else
		{
			// Group was not updated
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}

----------

#### Update a Group Permissions

Updates a group permissions.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :------------ | :--------------
`$rules`                     | array           | null          | array of rules and there value ( 0 or 1 )

returns `bool`

throws `Sentry\SentryException`

> **Note:** Permissions can also be updated through the `update()` method with a key of `permissions`

##### Example

	try
	{
		// Update current group permissions
		$permissions = array(
			'is_admin'   => 1, // add is_admin,
			'can_edit'   => 1, // add can_edit,
			'can_delete' => 0, // remove can_delete
		);

		if (Sentry::group('admin')->update_permissions($permissions))
		{
			// Group permissions were updated
		}
		else
		{
			// Group permissions were not updated
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
