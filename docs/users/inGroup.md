<a id="inGroup"></a>
###inGroup($group)

----------

Checks if a user is in a certain group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | mixed           | none          | Group Id, name or GroupInterface Ojbect

`returns` bool

####Example

	try
	{
		if ( Sentry::getUserProvider()->findById(1)->inGroup('admin') )
		{
			echo 'user is in group';
		}
		else
		{
			echo 'user is not in group';
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}