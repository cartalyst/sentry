<a id="remove-from-group" href="#"></a>
###remove_from_group($id)

----------

Removes a user from a group.

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
	    $user->remove_from_group(2);

	    // option 2
	    $user->remove_from_group('editor');
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user already not in group.
	}
