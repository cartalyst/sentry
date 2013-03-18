<a id="clear" href="#"></a>
###clear()

----------

The clear method removes attempts and suspensions for all, or the selected, users

> **Note:** If you have multiple Sentry_Objects, the attempts won't be updated when cleared unless you refresh that instance.

####Example

	// clear attempts for all users
	Sentry::attempts()->clear();

	// clear all attempts for login id
	Sentry::attempts('john.doe@domain.com')->clear();

	// clear all attempts for an ip
	Sentry::attempts(null, '123.432.21.1')->clear();

	// clear all attempts for a login/ip combo
	Sentry::attempts('john.doe@domain.com', '123.432.2.1')->clear();
