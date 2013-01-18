<a id="register"></a>
###register($credentials, $activate = false)

----------

Registers a user which requires activation.  If the user already exists but is not activated, it will create a new activation code.

Parameters          | Type                | Default             | Required            | Description
:------------------ | :------------------ | :------------------ | :------------------ | :------------------
`$credentials`      | array               | none                | true                | An array of user fields create a user with. The Login field is required, all other fields are optional.
`$activate`         | bool                | false               | false               | Whether or not to activate the user when it's registered

`returns` UserInterface
`throws`  LoginRequiredException, UserExistsException

####Example

	try
	{
		// Let's register a user. We won't activate them right now though
		// (we'd set the second parameter to 'true' to activate them)
		$user = Sentry::register(array(
			'email'    => 'testing@test.com',
			'password' => 'test'
		));

		// Let's get the activation code
		$activationCode = $user->getActivationCode();

		// Send activation code to user to activate their account
		...
	}
	catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
	{
		echo 'Login field required.';
	}
	catch (Cartalyst\Sentry\Users\UserExistsException $e)
	{
		echo 'User already exists.';
	}
