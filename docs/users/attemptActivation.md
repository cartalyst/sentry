<a id="attemptActivation"></a>
###attemptActivation($activationCode)

----------

Activates a user.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$activationCode`   | string              | none                | true                | Activation Code

`returns` bool
`throws`  UserNotFoundException

####Example

	try
	{
		// Find the user
		$user = Sentry::getUserProvider()->findById(1);

		// Attempt user activation
		if ($user->attemptActivation('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
		{
			// User activation passed
		}
		else
		{
			// User activation failed
		}
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User does not exist.';
	}
