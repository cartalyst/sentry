<a id="change-password" href="#"></a>
###change_password($password, $old_password)

----------

The change_password method updates the users password. Their old password is required as well.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$password`                  | string          |               | The users new password
`$old_password`              | string          |               | The users old password

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    // update the user
	    $user = Sentry::user(25);

	    if ($user->change_password('newpassword', 'oldpassword'))
	    {
	        // password has been updated
	    }
	    else
	    {
	        // something went wrong
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as incorrect old password
	}
