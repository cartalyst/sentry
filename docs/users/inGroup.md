<a id="inGroup"></a>
###inGroup($group)

----------

Checks if a user is in a certain group.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$group`            | GroupInterface      | none                | true                | GroupInterface instance

`returns` bool
`throws`  UserNotFoundException, GroupNotFoundException

####Example

	try
	{
		// Find the user
		$user  = Sentry::getUserProvider()->findById(1);

		// Find the group
		$admin = Sentry::getGroupProvider()->findByName('Admin');

		// Check if the user is in the admin group
		if ($user->inGroup($admin))
		{
			// User is in Admin group
		}
		else
		{
			// User is not in Admin group
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
