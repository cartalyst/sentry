#### Get Attempt Limit

The get_limit method returns the number of attempts allowed before a user is suspended.

returns `integer`

> **Note:** Use an existing object if one is available to prevent extra queries.
The result will be the same on all objects as it is just pulling from the config.

##### Example

	// Get attempt limit
	$attempts = Sentry::attempts()->get_limit();
