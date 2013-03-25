<a id="has-access" href="#"></a>
###has_access($resource = null)

----------

Checks users permissions to see if they contain a certain rule.

Parameters                   | Type                | Default       | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$resource`                  | null, string, array |               | rules that should be checked for access

`returns` bool

> **Note:** If left null, Sentry will check for the current bundle, controller and method being used in its designated laravel format, such as `bundle::controller@method`. This 'null' feature does not work in laravel yet until a pull request is accepted.  Until then, always pass in a parameter.

####Example

	try
	{
	    if (Sentry::user()->has_access('admin'))
	    {
	    	// user has admin access
	    }
	    else
	    {
	    	// user does not have admin access
	    }
	}
	catch (Sentry\SentryException $e)
	{
	    $errors = $e->getMessage(); // catch errors
	}
