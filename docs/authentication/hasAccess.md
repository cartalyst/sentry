<a id="hasAccess"></a>
###hasAccess

----------

Checks to see if a user been granted a certain permission.  This includes any permissions given to them by an groups they may be apart of as well.  Users may also have permissions with a value of '-1'. This value is used to deny users of permissions that may have been assigned to them from a group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$permission`                | string          | none          | Permissions name
`returns` bool

####Example

	try
	{
		if ( ! Sentry::user('test@test.com')->hasAccess('admin'))
		{
			// user does not have access, redirect them or whatever else you may want to do
		}
		else
		{
			// user has access to the given permission
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}