#### Find All Groups

This will return all the groups.

returns `array`

throws `Sentry\SentryException`

##### Example

	try
	{
		$groups = Sentry::group()->all();
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}

----------

#### Find a Group by it's Id or by it's Name

Find a group by it's Id or by it's Name.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$id`                        | int, string     | null            | The group id or name.

returns `array`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find a group by the group id
		$group = Sentry::group(25);

		// Find a group by the group name
		$group = Sentry::group('myadmin');
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
