<a id="removeGroup"></a>
###removeGroup

----------

Remove a user from a group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | mixed           | none          | Group Id, name or GroupInterface Ojbect

`returns` bool

####Example

	try
	{
		if ( Sentry::user()->findById(1)->removeGroup('admin') )
		{
			echo 'user removed from group';
		}
		else
		{
			echo 'user not removed from group';
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}