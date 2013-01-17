<a id="addGroup"></a>
###addGroup($group)

----------

Assign a group to a user.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$group`            | GroupInterface      | none                | true                | GroupInterface instance

`returns` void
`throws`  UserNotFoundException, GroupNotFoundException

####Example

	try
	{
		$user  = Sentry::getUserProvider()->findById(1);
		$group = Sentry::getGroupProvider()->findById(1);

		$user->addGroup($group);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group does not exist.';
	}
