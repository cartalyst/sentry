#### Check if Group Exists

This method checks if the provided group exists.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$name`                      | int, string     | null            | The group id or name.

returns `bool`

> **Note:** This is just a helper function. You do not need to add this check in
on group creation. Create already checks if the name already exists or not.

##### Example

	// Check if the group exists
	if (Sentry::group_exists('mygroup')) // or Sentry::group_exists(3)
	{
		// The group exists
	}
	else
	{
		// The group does not exist
	}

----------

#### Get All Group Permissions

This method returns all of the group permissions.

returns `array`

##### Example

	try
	{
		// Get the admin group permissions
		$permissions = Sentry::user('admin')->permissions();
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}

----------

#### Get All Users in Group

This method returns all users assigned to the provided group.

returns `array`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Get all the assigned users on group with ID 2
		$users = Sentry::group(2)->users();
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
