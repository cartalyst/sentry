<a id="inGroup"></a>
###inGroup($group)

----------

Checks if a user is in a certain group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :------------ | :--------------
`$group`                     | GroupInterface  | none          | GroupInterface instance

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$user  = Sentry::getUserProvider()->findById(1);
		$admin = Sentry::getGroupProvider()->findByName('Admin');

		if ($user->inGroup($admin))
		{
			echo 'User is in Admin group.';
		}
		else
		{
			echo 'User is not in Admin group.';
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}
