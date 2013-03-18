<a id="activate-user" href="#"></a>
###activate_user($login_column_value, $code, $encoded = true)

----------

The activate_user method activates a non-active user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_column_value`        | string          |               | The users login ( email or username ).
`$code`                      | string          |               | The users activation hash.
`$decode`                    | bool            | true          | If the login value needs to be decoded.

`returns` bool, array (user) `throws` Sentry\SentryException

####Example

	// try to log a user in
	try
	{
	    // log the user in
	    $activate_user = Sentry::activate_user('VGhpcyBpcyBhbiBlbmNvZG...', '93kavFY63S8jtala93a76fQ...');
	    // or
	    $activate_user = Sentry::activate_user('john.doe@domain.com', '93kavFY63S8jtala93a76fQ...', false);

	    if ($activate_user)
	    {
	        // the user is now activated - do your own logic
	    }
	    else
	    {
	        // user was not activated
	    }

	}
	catch (Sentry\SentryException $e)
	{
	    // issue activating the user
	    // store/set and display caught exceptions such as a suspended user with limit attempts feature.
	    $errors = $e->getMessage();
	}
