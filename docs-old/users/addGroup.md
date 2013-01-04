<a id="addGroup"></a>
###addGroup($group)

----------

Assign a user to a group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | mixed           | none          | Group Id, name or GroupInterface Ojbect

`returns` bool
`throws` GroupNotFoundException

####Example

	try
	{
		if ( Sentry::getUserProvider()->findById(1)->addGroup('admin') )
		{
			echo 'user added to group';
		}
		else
		{
			echo 'user not added to group';
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}
	catch (Cartalyst\Sentry\GroupNotFoundException $e)
	{
		echo 'Group does not exist';
	}