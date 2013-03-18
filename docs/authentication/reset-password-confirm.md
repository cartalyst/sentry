<a id="reset-password-confirm" href="#"></a>
###reset_password_confirm($login_column_value, $code, $decode = true)

----------

The reset_password_confirm method returns a boolean if the reset password has been confirmed or not. If it is confirmed, the passwords are updated in the database appropriately.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login_column_value`        | string          |               | The users login ( email or username ).
`$code`                      | string          |               | The password reset hash.
`$decode`                    | bool            | true          | If the login value needs to be decoded.

`returns` bool, array `throws` Sentry\SentryException

> **Note:** If the user logs in to their account before confirming the reset, the reset process will be nullified.

> **Note:** Please use the latest dev branch ( as of 6/28/2012 ) for this as it has a required change in `laravel/routing/route.php` as follows:

	line 78 - '(:any)' => '([a-zA-Z0-9\.\-_%=]+)', // adds an '=' sign
	line 89 - '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)', // adds an '=' sign


####Example

	try
	{
	    // confirm password reset
	    $confirm_reset = Sentry::reset_password_confirm('VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw==', '93kavFY63S8jtala93a76fQ...');
	    // or
	    $confirm_reset = Sentry::reset_password_confirm('john.doe@domain.com', '93kavFY63S8jtala93a76fQ...', true);

	    if ($confirm_reset)
	    {
	        // show success page or redirect to login - or whatever you want
	    }
	    else
	    {
	        // password was not reset - bad login/hash combo
	    }

	}
	catch (Sentry\SentryException $e)
	{
	    // issue activating the user
	    // store/set and display caught exceptions such as a user not existing or user is disabled
	    $errors = $e->getMessage();
	}
