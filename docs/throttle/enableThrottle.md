<a id="enableThrottle"></a>
###enableThrottle

----------

Enables authentication throttling. Throttling is enabled by default with base sentry drivers.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$limit`                     | int             | null          | Number of attempts to allow
`$minutes`                   | int             | null          | Suspension / Attempt reset time in minutes

`returns` void
`throws` ThrottleLimitException, ThrottleTimeException

####Example

	try
	{
		// set the throttle to 10 attempts with a 15minute suspension/clear time
		Sentry::enableThrottle(10, 15);
	}
	catch (Cartalyst\Sentry\ThrottleLimitException $e)
	{
		// invalid limit value passed
	}
	catch (Cartalyst\Sentry\ThrottleTimeException $e)
	{
		// invalid throttle time passed
	}