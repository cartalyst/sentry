<a id="update" href="#"></a>
###update(array $fields, $hash_password = true)

----------

The update method can be used to update any field in the 'users' or 'users_metadata' tables for a user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$fields`                    | array           |               | An array consisting of fields to update. Metadata fields will need to be an array within a metadata key.
`$hash_password`             | bool            | true          | If set to true, all password fields will be hashed.

`returns` bool `throws` Sentry\SentryException

> **Note:** The register method checks to make sure the login and emails are unique. Register is typically used for the user registration process unless you do not want activation.

####Example

	try
	{
	    // update the user
	    $user = Sentry::user(25);
	    $update = $user->update(array(
	        'password' => 'somenewpassword',
	        'metadata' => array(
	            'first_name' => 'John',
	            'last_name'  => 'Doe'
	        )
	    ));

	    if ($update)
	    {
	        // the user was updated
	    }
	    else
	    {
	        // something went wrong
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user not existing or bad fields
	}
