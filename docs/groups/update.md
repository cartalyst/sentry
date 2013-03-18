<a id="update" href="#"></a>
###update(array $fields)

----------

The update method updates the current group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$fields`                    | array           |               | Array of fields to update for the group.

`returns` bool `throws` Sentry\SentryException

####Example

	// update a group
	try
	{
	    $group = Sentry::group(4);
	    $update = $group->update(array(
	        'name'        => 'New Name',
	        'permissions' => array(
	        	'is_admin' => 1,
	        ),
	    ));

	    if ($update)
	    {
	        // group was updated
	    }
	    else
	    {
	        // group was not updated
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
