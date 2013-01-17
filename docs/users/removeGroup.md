<a id="removeGroup"></a>
###removeGroup($group)

----------

Remove a user from a group.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$group`            | GroupInterface      | none                | true                | GroupInterface instance

`returns` bool
`throws`  UserNotFoundException, GroupNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Find the group
		$group = Sentry::getGroupProvider()->findByName('Admin');

		// Check if the user was removed from the group
		if ($user->removeGroup($admin))
		{
			// User removed from Admin group
		}
		else
		{
			// User not removed from Admin group
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
