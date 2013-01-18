<a id="findById"></a>
###findById($id)

----------

Find a user by their ID.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$id`               | int                 | none                | true                | User's ID

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User not found.';
	}
