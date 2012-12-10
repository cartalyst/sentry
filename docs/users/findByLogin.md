<a id="findByLogin"></a>
###findByLogin($login)

----------

Find a user by their login id.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | User's login id

`returns` UserInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::user()->findByLogin('john.doe@platform.com');
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User not found.';
	}