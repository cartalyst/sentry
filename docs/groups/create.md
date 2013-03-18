<a id="create" href="#"></a>
###create($group)

----------

The create method creates a new group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$group`                     | array           |               | An array of consisting of the groups 'name'. Optional fields are 'permissions'. The group name must be unique.

`returns` bool, int - false or group id `throws` Sentry\SentryException

####Example

	// create a group
	try
	{
	    $group_id = Sentry::group()->create(array(
	        'name'  => 'myadmin'
	    ));
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
