#### Remove a User from a Group

Removes a user from a group.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$id`                        | int, string     | null            | The groups id or name

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Option 1
		$user->remove_from_group(2);

		// Option 2
		$user->remove_from_group('editor');
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
