<a id="findByName"></a>
###findByName($name)

----------

Find a group by its id.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$name`                        | int             | none          | Group's name

`returns` GroupInterface
`throws`  GroupNotFoundException

####Example

	try
	{
		$group = Sentry::group()->findByName('admin');
	}
	catch (Cartalyst\Sentry\GroupNotFoundException $e)
	{
		echo 'Group not found.';
	}