<a id="add-to-group" href="#"></a>
###add_to_group($id)

----------

Adds the user to a group.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$id`                        | int, string     |               | The groups id or name

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    // find the user
	    $user = Sentry::user(25);

	    // option 1
	    $user->add_to_group(2);

	    // option 2
	    $user->add_to_group('editor');
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user already in group
	}
