<a id="get" href="#"></a>
###get()

----------

The get method returns the number of failed attempts a login / ip combo has tried.

`returns` integer, array - number of failed attempts, array of associated login/ip attempts

####Example

	// get attempts

	// for all cases
	$attempts = Sentry::attempts()->get(); // returns array

	// for a single case
	$attempts = Sentry::attempts('john.doe@domain.com', '123.432.2.1')->get(); // returns int

	// for all attempts associated to a username or ip
	$attempts = Sentry::attempts('john.doe@domain.com')->get(); // returns array
	$attempts = Sentry::attempts(null, '123.432.2.1')->get(); // returns array
