#### Find All Users

The all method retrieves all users

returns `array`

##### Example

	// Grab all users
	$users = Sentry::user()->all();

----------

#### Find a User

The user method returns a Sentry_User object.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$id`                     	 | int, string     | null            | The users id (int) or login column value (string).<br>If null, the current logged in user is selected.<br>If there is no user logged in, a blank user object is returned.
`$recache`                   | bool            | false           | Recache the selected user object.

returns `bool`

throws `Sentry\SentryException`

##### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(12);

		or

		// Find a user using the using login column value
		$user = Sentry::user('john.doe@example.com');

		or

		// Get the current logged in user or an empty user
		$user = Sentry::user();
	}
	catch (Sentry\SentryException $e)
	{
		$error = $e->getMessage();
	}
