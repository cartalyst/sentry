<a id="group-exists" href="#"></a>
###group_exists($name)

----------

The group_exists methods returns a bool of whether the group exists or not.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$name`                      | int, string     | null          | The groups id or name.

`returns` bool

> **Note:** This is just a helper function. You do not need to add this check in on group creation creation. Create already checks if the name already exists or not.

####Example

	// check if group exists
	if (Sentry::group_exists('mygroup')) // or Sentry::group_exists(3)
	{
	    // the group exists
	}
	else
	{
	    // the group does not exist
	}
