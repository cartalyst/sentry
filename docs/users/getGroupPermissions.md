<a id="getGroupPermissions"></a>
###getGroupPermissions

----------

Gets the users group permissions

`returns` array

####Example

	try
	{
		$groupPermissions = Sentry::user()->findById(1)->getGroupPermissions();
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}