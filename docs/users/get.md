<a id="get" href="#"></a>
###get($field = null)

----------

The get method returns requested fields.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$field`                     | string, array  |               | A field or array of fields to return from the 'users' table. To retrieve metadata use 'metadata'.

`returns` bool `throws` Sentry\SentryException

####Example

	try
	{
	    // select the user
	    $user = Sentry::user(25);

	    // option 1
	    $email = $user->get('email');
	    $metadata = $user->get('metadata');

	    // option 2
	    $user_data = $user->get(array('email', 'metadata'));

	    // option 3
	    $first_name = $user->get('metadata.first_name');
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors such as user not existing or bad fields
	}
