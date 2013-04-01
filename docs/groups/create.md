### Create a new Group

The create method creates a new group.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$group`                     | array           | null            | An array of consisting of the groups 'name'. Optional fields are 'permissions'. The group name must be unique.

returns `bool` or `group id`

throws `Sentry\SentryException`

----------

#### Example

	try
	{
		// Create the group
		$group_id = Sentry::group()->create(array(
			'name'  => 'myadmin',
		));
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
