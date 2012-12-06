<a id="findById"></a>
###findById

----------

Find a group by its id.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | int             | none          | Group's id

`returns` GroupInterface
`throws`  GroupNotFoundException

####Example

	try
	{
		$group = Sentry::group()->findById(1);
	}
	catch (Cartalyst\Sentry\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}