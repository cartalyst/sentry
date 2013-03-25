#### Create a new User

The create method creates a user.

Parameters                   | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$user`                      | array           | null            | An array consisting of the 'username', 'email', and 'password' and their values. If 'email' is the login column, 'username' is not required.
`$activation`                | bool            | false           | If set to true, the user is required to activate their account before they can log in.

returns `integer` , `array` - `user_id`, or an `array` of user_id and activation hash.

throws `Sentry\SentryException`

> **Note:** The create method checks to make sure the login and emails are unique.
Create is typically used for admin user creation.

##### Example

	try
	{
		// Prepare the user data
		$vars = array(
			'email'    => 'john.doe@example.com',
			'password' => 'mypass',
			'metadata' => array(
				'first_name' => 'John',
				'last_name'  => 'Doe',
				// add any other fields you want in your metadata here. ( must add to db table first )
			)
		);

		// Create the user
		if ($user_id = Sentry::user()->create($vars))
		{
			// the user was created - send email notifying user account was created
		}
		else
		{
			// something went wrong - shouldn't really happen
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
