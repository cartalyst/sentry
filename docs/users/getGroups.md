<a id="getGroups"></a>
###getGroups()

----------

Retrieves the users' groups.

`returns` GroupInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$user   = Sentry::getUserProvider()->findById(1);
		$groups = $user->getGroups();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
