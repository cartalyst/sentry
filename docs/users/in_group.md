<a id="in-group" href="#"></a>
###in_group($name)

----------

Checks to see if the user is in a group

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$name`                      | int, string     |               | The groups id or name

`returns` bool

####Example

	try
	{
	    // check to see if the current user is in the editors(id:2) group
		if (Sentry::user()->in_group(2)) // or in_group('editors');
		{
		    // user is in the group
		}
		else
		{
		    // user is not in the group
		}
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user does not exist.
	}
