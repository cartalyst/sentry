<a id="findById"></a>
###findById($id)

----------

Find a user by their id.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | int             | none          | User's id

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found.';
	}