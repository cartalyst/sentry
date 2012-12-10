<a id="activate"></a>
###activate($activationCode)

----------

Activate a user.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$activationCode`            | string          | none          | Activation Code

`returns` bool

####Example

	try
	{
		if (Sentry::user()->findByLogin('test@test.com')->activate('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
		{
			echo 'activated';
		}
		else
		{
			echo 'activation failed';
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
		echo 'User does not exist';
	}