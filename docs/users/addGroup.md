<a id="addGroup"></a>
###addGroup($group)

----------

Assign a group to a user.

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
		$group = Sentry::getGroupProvider()->findById(1);

		// Check if the user was assigned to the group
		if ($user->addGroup($group))
		{
			// User assigned to the Admin group
		}
		else
		{
			// User was not assigned to the Admin group
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
