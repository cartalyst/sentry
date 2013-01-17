<a id="removeGroup"></a>
###removeGroup($group)

----------

Remove a user from a group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$group`                     | GroupInterface  | none          | GroupInterface instance

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		$user = Sentry::getUserProvider()->findById(1);
		$group = Sentry::getGroupProvider()->findByName('Admin');

		if ($user->removeGroup($admin))
		{
			echo 'User removed from Admin group.';
		}
		else
		{
			echo 'User not removed from Admin group.';
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group does not exist.';
	}
