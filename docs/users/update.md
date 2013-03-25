### Update a User

The update method can be used to update any field in the 'users' or 'users_metadata' tables for a user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$fields`                    | array           |               | An array consisting of fields to update. Metadata fields will need to be an array within a metadata key.
`$hash_password`             | bool            | true          | If set to true, all password fields will be hashed.

returns `bool`

throws `Sentry\SentryException`

> **Note:** The register method checks to make sure the login and emails are unique. Register is typically used for the user registration process unless you do not want activation.

----------

#### Example

	try
	{
		// Find the user using the user id
		$user = Sentry::user(25);

		// Prepare the user data to be updated
		$user_data = array(
			'password' => 'somenewpassword', // You can change the user password here aswell
			'metadata' => array(
				'first_name' => 'John',
				'last_name'  => 'Doe',
			),
		));

		// Update the user
		if ($user->update($user_data))
		{
			// User information was updated
		}
		else
		{
			// User information was not updated
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
