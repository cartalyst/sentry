<a id="login" href="#"></a>
###login($login_column_value, $password, $remember = false)

----------

The login method logs the user in.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_column_value`        | string          |               | The users login ( email or username ).
`$password`                  | string          |               | The users password.
`$remember`                  | bool            | false         | Whether the remember me cookie should be created or not.

`returns` bool `throws` Sentry\SentryException

####Example

	// try to log a user in
	try
	{
	    // log the user in
	    $valid_login = Sentry::login('john.doe@domain.com', 'secretpassword', true);

	    if ($valid_login)
	    {
	        // the user is now logged in - do your own logic
	    }
	    else
	    {
	        // could not log the user in - do your bad login logic
	    }

	}
	catch (Sentry\SentryException $e)
	{
	    // issue logging in via Sentry - lets catch the sentry error thrown
	    // store/set and display caught exceptions such as a suspended user with limit attempts feature.
	    $errors = $e->getMessage();
	}
