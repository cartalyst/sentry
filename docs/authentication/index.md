###user($id = null, $recache = false)

----------

The user method returns a Sentry_User object.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$id`                     	 | int, string     | null            | The users id (int) or login column value (string).<br>If null, the current logged in user is selected.<br>If there is no user logged in, a blank user object is returned.
`$recache`                   | bool            | false           | Recache the selected user object.

`returns` bool

`throws` Sentry\SentryException

##### Example

	// select a user by id
	try
	{
		$user = Sentry::user(12);

		// select a user by login column
		$user = Sentry::user('john.doe@example.com');

		// get the current logged in user or an empty user
		$user = Sentry::user();
	}
	catch (Sentry\SentryException $e)
	{
		$error = $e->getMessage();
	}
