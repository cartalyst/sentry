#### Get Attempts

The attempts method sets and returns a Sentry_Attempts object.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_id`                  | string          | null          | The users login.
`$ip_address`                | string          | null          | The users ip address.

returns `Sentry_Attempts`

throws `Sentry\SentryException`

> **Note:** The exceptions with this method are only thrown if there are bad
config settings. You should only have to worry about them during the inital
setup of Sentry.

##### Example

	// Get all attempts
	$attempts = Sentry::attempts();

	// Get attempts of a user
	$attempts = Sentry::attempts('john.doe@example.com');

	// Get attempts an ip address
	$attempts = Sentry::attempts(null, '123.432.2.1');

	// Get attempts of a single user/ip combo
	$attempts = Sentry::attempts('john.doe@example.com', '123.432.2.1');
