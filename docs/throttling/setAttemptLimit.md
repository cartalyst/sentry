<a id="setAttemptLimit"></a>
###setAttemptLimit($limit)

----------

Sets the number of attempts allowed before suspension.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$limit`                     | int             | none          | Number of attempts allowed. Default is set by driver (5).  This is used to override the driver default.

`returns` void
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
		$throttle->setAttemptLimit(3);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
