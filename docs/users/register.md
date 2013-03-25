### Register a User

The register method creates a user. It is just an alias for the create method
with activation set to true.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$user`                      | array           |               | An array consisting of the 'username', 'email', and 'password' and their values. If 'email' is the login column, 'username' is not required.

`returns` array - array of user_id and activation hash.

`throws` Sentry\SentryException

> **Note:** The register method checks to make sure the login and emails are
unique. Register is typically used for the user registration process unless you
do not want activation.

----------

#### Example

	try
	{
		// create the user
		$user = Sentry::user()->register(array(
			'email'    => 'john.doe@domain.com',
			'password' => 'mypass',
			'metadata' => array(
				'first_name' => 'John',
				'last_name'  => 'Doe',
				// add any other fields you want in your metadata here. ( must add to db table first )
			)
		));

		if ($user)
		{
			// the user was created
			$link = 'domain.com/auth/activate/'.$user['hash'];

			// send email with link to activate.
		}
		else
		{
			// something went wrong - shouldn't really happen
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage(); // catch errors such as user exists or bad fields
	}
