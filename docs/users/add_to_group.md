#### Add a User to a Group

Adds the user to a group.

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
		$user->add_to_group(2);

		// Option 2
		$user->add_to_group('editor');
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
