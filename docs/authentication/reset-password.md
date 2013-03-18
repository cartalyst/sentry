<a id="reset-password" href="#"></a>
###reset_password($login_column_value, $password)

----------

The reset_password method returns an array or false. The new password and reset hash will be stored in temporary fields in the database.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_column_value`        | string          |               | The users login ( email or username ).
`$password`                  | string          | true          | A new password that the user wants to use.

`returns` bool, array `throws` Sentry\SentryException

> **Notes:** The new password will not come into effect until confirmed. Also, if a user logs into the account before the password is confirmed, the entire reset password process will be nullified

####Example

	try
	{
	    // reset the password
	    $reset = Sentry::reset_password('john.doe@domain.com', 'newpassword');

	    if ($reset)
	    {
	        $email = $reset['email'];
	        $link = 'domain.com/auth/reset_password_confirm/'.$reset['link']; // adjust path as needed

	        // email $link to $email
	    }
	    else
	    {
	        // password was not reset
	    }

	}
	catch (Sentry\SentryException $e)
	{
	    // issue activating the user
	    // store/set and display caught exceptions such as a user not existing or user is disabled
	    $errors = $e->getMessage();
	}
