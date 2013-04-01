#### Clear User(s) Suspension(s)

The clear method removes attempts and suspensions for all, or the selected, users.

> **Note:** If you have multiple Sentry_Objects, the attempts won't be updated
when cleared unless you refresh that instance.

##### Example

	// Clear attempts for all users
	Sentry::attempts()->clear();

	// Clear all attempts for login id
	Sentry::attempts('john.doe@example.com')->clear();

	// Clear all attempts for an ip
	Sentry::attempts(null, '123.432.21.1')->clear();

	// Clear all attempts for a login/ip combo
	Sentry::attempts('john.doe@example.com', '123.432.2.1')->clear();
