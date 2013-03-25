#### Confirm a Reset User Password

The reset_password_confirm method returns a boolean if the reset password has been confirmed or not. If it is confirmed, the passwords are updated in the database appropriately.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column_value`        | string          | null            | The users login ( email or username ).
`$code`                      | string          | null            | The password reset hash.
`$decode`                    | bool            | true            | If the login value needs to be decoded.

returns `bool` or `array`

throws `Sentry\SentryException`

> **Note:** If the user logs in to their account before confirming the reset, the reset process will be nullified.

> **Note:** Please use the latest dev branch ( as of 6/28/2012 ) for this as it has a required change in `laravel/routing/route.php` as follows:

	line 78 - '(:any)' => '([a-zA-Z0-9\.\-_%=]+)', // adds an '=' sign
	line 89 - '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)', // adds an '=' sign

##### Example

	try
	{
		// Confirm the user password reset code
		$confirm_reset = Sentry::reset_password_confirm('VGhpcyBpcyBhbiBlbmNvZG...', '93kavFY63S8jtala93a76fQ...');

		# or

		// Confirm the user password reset code using a non hashed login value
		$confirm_reset = Sentry::reset_password_confirm('john.doe@example.com', '93kavFY63S8jtala93a76fQ...', false);

		if ($confirm_reset)
		{
			// User password was successfully changed
		}
		else
		{
			// There was a problem confirming the password reset code
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
