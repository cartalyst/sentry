<a id="findByCredentials"></a>
###findByCredentials($credentials)

----------

Find a user by an array of credentials, which must include the login column. Hashed fields will be hashed and checked against their value in the database.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$credentials`      | array               | none                | true                | Array of credentials to check against. Login field is required.

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findByCredentials(array(
			'email'      => 'john.doe@platform.com',
			'password'   => 'test',
			'first_name' => 'John',
		));
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
	}
