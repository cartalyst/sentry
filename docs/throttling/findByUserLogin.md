<a id="findByUserLogin"></a>
###findByuserLogin($login)

----------

Retrieves a throttle object based on the user login provided. Will always retrieve a throttle object.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$login`            | string              | none                | true                | User's login ID

`returns` ThrottleInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserLogin('john.doe@platform.com');
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
