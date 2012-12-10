<a id="findByCreentials"></a>
###findByCredentials($credentials)

----------

Find a user by an array of credentials, which must include the login column. Hashed fields will be hashed and checked against their value in the database.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials`               | array           | none          | array of credentials to check against. Login field is required.

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::user()->findByCredentials(array(
			'email'    => 'john.doe@platform.com',
			'password' => 'test',
			'first_name' => 'John',
		));
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found.';
	}