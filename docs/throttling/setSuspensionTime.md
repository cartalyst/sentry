<a id="setSuspensionTime"></a>
###setSuspensionTime($minutes)

----------

Sets the length of the suspension.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$mintues`                   | int             | none          | Length of the suspension in minutes. Default is set by driver (15).  This is used to override the driver default.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->setSuspensionTime(10);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
