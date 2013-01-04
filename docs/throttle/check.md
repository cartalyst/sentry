<a id="check"></a>
###check($login)

----------

Checks a logins throttle status

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` true
`throws`  UserBannedException, UserSuspendedException

####Example

	try
	{
		Sentry::getThrottleProvider()->check('test@test.com');
	}
	catch (Cartalyst\Sentry\UserBannedException $e)
	{
		ehco 'user is banned.';
	}
	catch (Cartalyst\Sentry\UserSuspendedException $e)
	{
		echo 'user is suspended for '.Sentry::getThrottleProvider()->getSuspensionTime().' minutes.';
	}