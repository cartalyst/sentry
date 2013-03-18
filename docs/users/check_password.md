<a id="check-password" href="#"></a>
###check_password($password, $field = 'password')

----------

The check_password method checks to make sure a given password matches the password stored in the database.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$password`                  | string          |               | The password to check
`$field`                     | string          | 'password'    | The column to check against

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    // find the user
	    $user = Sentry::user(25);

	    if ($user->check_password('mypassword'))
	    {
	        // password matches
	    }
	    else
	    {
	        // something went wrong
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors
	}
