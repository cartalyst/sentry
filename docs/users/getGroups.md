<a id="getGroups"></a>
###getGroups()

----------

Retrieves the users groups.

`returns` GroupInterface

####Example

	try
	{
		$groups = Sentry::user()->findById(1)->getGroups();
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}