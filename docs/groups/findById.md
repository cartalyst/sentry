<a id="findById"></a>
###findById($id)

----------

Finds a group by it's id.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$id`               | int                 | none                | true                | Group's id

`returns` GroupInterface
`throws`  GroupNotFoundException

####Example

	try
	{
		$group = Sentry::getGroupProvider()->findById(1);
	}
	catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}
