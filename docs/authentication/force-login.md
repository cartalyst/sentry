<a id="force-login" href="#"></a>
###force_login($id, $provider = 'Sentry-Forced')

----------

Forces a login. A user will be logged in as long as that user exists.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | int, string     |               | The user id or loing ( email or username ).
`$provider`                  | string          | Sentry-Forced | What system was used to force the login

`returns` bool `throws` Sentry\SentryException

####Example

	// basic force_login  example
	try
	{
	    // force login
	    Sentry::force_login('john.doe@domain.com');
	}
	catch (Sentry\SentryException $e)
	{
	    // could not for the login - user not found
	    $error = $e->getMessage();
	}
