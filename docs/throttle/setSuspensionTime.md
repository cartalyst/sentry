<a id="setSuspensionTime"></a>
###setSuspensionTime($mintues)

----------

Sets the length of the suspension.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$mintues`                   | int             | none          | Length of the suspension in minutes. Default is set by driver (15).  This is used to override the driver default.

`returns` void
`throws` ThrottleTimeException

####Example

	try
	{
		Sentry::getThrottleProvider()->setSuspensionTime(10);
	}
	catch (Cartalyst\Sentry\ThrottleTimeException $e)
	{
		echo 'Invalid throttle time passed. Must be an integer.'
	}