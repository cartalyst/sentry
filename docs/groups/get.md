<a id="get" href="#"></a>
###get($field = null)

----------

The update method updates the current group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$field`                     | string, array   | null          | The name or names of fields to retrieve.

`returns` string, array `throws` Sentry\SentryException

####Example

	// get group information
	try
	{
		$group_info = Sentry::group(2)->get('name');
		//or
	    $group_info = Sentry::group(2)->get(array('name', 'permissions'));
	    //or
	    $group_info = Sentry::group(2)->get();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
