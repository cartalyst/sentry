<a id="findByLogin"></a>
###findByLogin($login)

----------

Find a user by their login ID.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$login`            | string              | none                | true                | User's login ID

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findByLogin('john.doe@platform.com');
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
	}
