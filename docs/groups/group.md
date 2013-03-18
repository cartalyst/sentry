<a id="group" href="#"></a>
###group($id = null)

----------

The group method grabs and sets all group information. It does not return anything.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | int, string     | null          | The group id or name.

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
		// set the group
	    $group = Sentry::group(25);

	    // or
	    $group = Sentry::group('somegroup');

	    //or
	    $group = Sentry::group();
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage();
	}
