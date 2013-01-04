<a id="setAttemptLimit"></a>
###setAttemptLimit($limit)

----------

Sets the number of attempts allowed before suspension.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$limit`                     | int             | none          | Number of attempts allowed. Default is set by driver (5).  This is used to override the driver default.

`returns` void
`throws` `throws` ThrottleTimeException

####Example

	try
	{
		Sentry::getThrottleProvider()->setAttemptLimit(3);
	}
	catch (Cartalyst\Sentry\`throws` ThrottleTimeException $e)
	{
		echo 'Invalid throttle limit passed. Must be an integer.'
	}