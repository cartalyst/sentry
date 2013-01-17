<a id="findByName"></a>
###findByName($name)

----------

Finds a group by it's name.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$name`             | string              | none                | true                | Group's name

`returns` GroupInterface
`throws`  GroupNotFoundException

####Example

	try
	{
		$group = Sentry::getGroupProvider()->findByName('admin');
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}
