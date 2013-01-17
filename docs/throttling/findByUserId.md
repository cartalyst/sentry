<a id="findByUserId"></a>
###findByuserId($id)

----------

Retrieves a throttle object based on the user ID provided. Will always retrieve a throttle object.

`returns` ThrottleInterface
`throws`  UserNotFoundException

####Example

	try
	{
		$throttle = Sentry::getThrottleProvider()->findByUserId(1);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
