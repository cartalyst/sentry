<a id="attempts" href="#"></a>
###attempts($login_id = null, $ip_address = null)

----------

The attempts method sets and returns a Sentry_Attempts object.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_id`                  | string          | null          | The users login.
`$ip_address`                | string          | null          | The users ip address.

`returns` Sentry_Attempts `throws` Sentry\SentryException

> **Note:** The exceptions with this method are only thrown if there are bad config settings. You should only have to worry about them during the inital setup of Sentry.

####Example

	// get the Sentry_Attempts object
	// sets attempts data for all users
	$attempts = Sentry::attempts();

	// sets attempts data for a user
	$attempts = Sentry::attempts('john.doe@domain.com');

	// set attempts data for an ip address
	$attempts = Sentry::attempts(null, '123.432.2.1');

	// set attempts for a single user/ip combo
	$attempts = Sentry::attempts('john.doe@domain.com', '123.432.2.1');
