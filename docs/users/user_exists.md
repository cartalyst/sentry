<a id="user-exists" href="#"></a>
###user_exists($login_column_value)

----------

The user_exists methods returns a bool of whether the user exists or not.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column_value`        | int, string     |               | The user_id or users login.

`returns` bool

> **Note:** This is just a helper function. You do not need to add this check in on registration or user creation. These methods already check to make sure the user is unique by calling this method.

####Example

	if (Sentry::user_exists('john.doe@domain.com'))
	{
	    // the user exists
	}
	else
	{
	    // the user does not exist
	}
