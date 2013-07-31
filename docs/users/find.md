### Finding Users

Finding users can sometimes be difficult and harsh, well, Sentry provides you
simple methods to find your users.

----------

#### Exceptions

##### Cartalyst\Sentry\Users\UserNotFoundException

If the provided user was not found, this exception will be thrown.

----------

#### Get the Current Logged in User

Returns the user that's set with Sentry, does not check if a user is logged in
or not. To do that, use [`check()`](/sentry-2/authentication/helpers#check-if-the-user-is-logged-in) instead.

##### Example

	try
	{
		// Get the current active/logged in user
		$user = Sentry::getUser();
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		// User wasn't found, should only happen if the user was deleted
		// when they were already logged in or had a "remember me" cookie set
		// and they were deleted.
	}

----------

#### Find all the Users

This will return all the users.

##### Example

	$users = Sentry::findAllUsers();

----------

#### Find all the Users with access to a permissions(s)

Finds all users with access to a permission(s).

#### Example

	// Feel free to pass a string for just one permission instead
	$users = Sentry::findAllUsersWithAccess(array('admin', 'other'));

----------

#### Find all the Users in a Group

Finds all users assigned to a group(s).

#### Example

	$users = Sentry::findAllUsersInGroup(array('administrator', 'users'));

----------

#### Find a User by their Credentials

Find a user by an array of credentials, which must include the login column. Hashed fields will be hashed and checked against their value in the database.

##### Example

	try
	{
		$user = Sentry::findUserByCredentials(array(
			'email'      => 'john.doe@example.com',
			'password'   => 'test',
			'first_name' => 'John',
		));
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}

----------

#### Find a User by their Id

Find a user by their ID.

##### Example

	try
	{
		$user = Sentry::findUserById(1);
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}

----------

#### Find a User by their Login Id

Find a user by their login ID.

##### Example

	try
	{
		$user = Sentry::findUserByLogin('john.doe@example.com');
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}

----------

#### Find a User by their Activation Code

Find a user by their registration activation code.

##### Example

	try
	{
		$user = Sentry::findUserByActivationCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb');
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}

----------

#### Find a User by their Reset Password Code

Find a user by their reset password code.

##### Example

	try
	{
		$user = Sentry::findUserByResetPasswordCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb');
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}
