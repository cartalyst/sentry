## Users

In this section we'll cover how you can create or register users, assign groups and permissions to users.

### Sentry::createUser()

To create a new user you need to pass an `array()` of user fields into the `create()` method, please note, that the `login` field and the password are required, all the other fields are optional.

Param        | Required | Default | Type  | Description
------------ | -------- | ------- | ----- | -----------------------------------
$credentials | true     | null    | array | The user credentials and attributes.

#### Examples

Create a User and assign this new user an existing group.

```php
try
{
	// Create the user
	$user = Sentry::createUser(array(
		'email'     => 'john.doe@example.com',
		'password'  => 'test',
		'activated' => true,
	));

	// Find the group using the group id
	$adminGroup = Sentry::findGroupById(1);

	// Assign the group to the user
	$user->addGroup($adminGroup);
}
catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
{
	echo 'Login field is required.';
}
catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
{
	echo 'Password field is required.';
}
catch (Cartalyst\Sentry\Users\UserExistsException $e)
{
	echo 'User with this login already exists.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

Create a new user and set permissions on this user.

This example does pretty much the same as the previous one with the exception that we are not assigning him any group, but we are granting this user some permissions.

```php
try
{
	// Create the user
	$user = Sentry::createUser(array(
		'email'       => 'john.doe@example.com',
		'password'    => 'test',
		'activated'   => true,
		'permissions' => array(
			'user.create' => -1,
			'user.delete' => -1,
			'user.view'   => 1,
			'user.update' => 1,
		),
	));
}
catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
{
	echo 'Login field is required.';
}
catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
{
	echo 'Password field is required.'
}
catch (Cartalyst\Sentry\Users\UserExistsException $e)
{
	echo 'User with this login already exists.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\LoginRequiredException      | When you don't provide the required `login` field, this exception will be thrown.
Cartalyst\Sentry\Users\PasswordRequiredException   | When you don't provide the `password` field, this exception will be thrown.
Cartalyst\Sentry\Users\UserExistsException         | This exception will be thrown when the user you are trying to create already exists on your database.
Cartalyst\Sentry\Groups\GroupNotFoundException     | This exception will be thrown when the group that's being assigned to the user doesn't exist.

### Sentry::register()

Registering a user will require the user to be manually activated but you can bypass this passing a boolean of `true` as a second parameter.

If the user already exists but is not activated, it will create a new activation code.

Param        | Required | Default | Type    | Description
------------ | -------- | ------- | ------- | -----------------------------------
$credentials | true     | null    | array   | The user credentials and attributes.
$activate    | false    | false   | boolean | Flag to wether activate the user or not.

#### Example

```php
try
{
	// Let's register a user.
	$user = Sentry::register(array(
		'email'    => 'john.doe@example.com',
		'password' => 'test',
	));

	// Let's get the activation code
	$activationCode = $user->getActivationCode();

	// Send activation code to the user so he can activate the account
}
catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
{
	echo 'Login field is required.';
}
catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
{
	echo 'Password field is required.';
}
catch (Cartalyst\Sentry\Users\UserExistsException $e)
{
	echo 'User with this login already exists.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\LoginRequiredException      | When you don't provide the required `login` field, this exception will be thrown.
Cartalyst\Sentry\Users\PasswordRequiredException   | When you don't provide the required `password` field, this exception will be thrown.
Cartalyst\Sentry\Users\UserExistsException         | This exception will be thrown when the user you are trying to create already exists on your database.

### Update a User

Updating users information is very easy with Sentry, you just need to find the user you want to update and update their information. You can add or remove groups from users as well.

#### Examples

In this example we are just updating the user information.

> **Note:** If you provide another email address, and that email address is already registered in your system, the following Exception `Cartalyst\Sentry\Users\UserExistsException` will be thrown.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Update the user details
	$user->email = 'john.doe@example.com';
	$user->first_name = 'John';

	// Update the user
	if ($user->save())
	{
		// User information was updated
	}
	else
	{
		// User information was not updated
	}
}
catch (Cartalyst\Sentry\Users\UserExistsException $e)
{
	echo 'User with this login already exists.';
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

**Assign a new Group to a User**

In this example we are assigning the provided Group to the provided User.

> **Note:** If the provided Group is not found an Exception `Cartalyst\Sentry\Groups\GroupNotFoundException` will be thrown.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Find the group using the group id
	$adminGroup = Sentry::findGroupById(1);

	// Assign the group to the user
	if ($user->addGroup($adminGroup))
	{
		// Group assigned successfully
	}
	else
	{
		// Group was not assigned
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

**Remove a Group from the User**

In this example we are removing the provided Group from the provided User.

> **Note:** If the provided Group is not found an Exception `Cartalyst\Sentry\Groups\GroupNotFoundException` will be thrown.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Find the group using the group id
	$adminGroup = Sentry::findGroupById(1);

	// Assign the group to the user
	if ($user->removeGroup($adminGroup))
	{
		// Group removed successfully
	}
	else
	{
		// Group was not removed
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

**Update the User details and assign a new Group**

This is a combination of the previous examples, where we are updating the user information and assigning a new Group the provided User.

> **Note:** If the provided Group is not found an Exception `Cartalyst\Sentry\Groups\GroupNotFoundException` will be thrown.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Find the group using the group id
	$adminGroup = Sentry::findGroupById(1);

	// Assign the group to the user
	if ($user->addGroup($adminGroup))
	{
		// Group assigned successfully
	}
	else
	{
		// Group was not assigned
	}

	// Update the user details
	$user->email = 'john.doe@example.com';
	$user->first_name = 'John';

	// Update the user
	if ($user->save())
	{
		// User information was updated
	}
	else
	{
		// User information was not updated
	}
}
catch (Cartalyst\Sentry\Users\UserExistsException $e)
{
	echo 'User with this login already exists.';
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\LoginRequiredException      | When you don't provide the required `login` field, this exception will be thrown.
Cartalyst\Sentry\Users\UserExistsException         | This exception will be thrown when the user you are trying to create already exists in your database.
Cartalyst\Sentry\Users\UserNotFoundException       | If the provided user was not found, this exception will be thrown.
Cartalyst\Sentry\Groups\GroupNotFoundException     | This exception will be thrown when the group that's being assigned to the user doesn't exist.

### Delete a user

Deleting users is very simple and easy.

#### Example

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Delete the user
	$user->delete();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\UserNotFoundException       | If the provided user was not found, this exception will be thrown.

### Activating a User

User activation is very easy with Sentry, you need to first find the user you want to activate, then use the `attemptActivation()` method and provide the activation code, if the activation passes it will return `true` otherwise, it will return `false` .

> **Note:** If the user you are trying to activate, is already activated, the following Exception `Cartalyst\Sentry\Users\UserAlreadyActivatedException` will be thrown.

#### Example

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Attempt to activate the user
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
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Users\UserAlreadyActivatedException $e)
{
	echo 'User is already activated.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                            | Description
---------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\UserAlreadyActivatedException | If the provided user is already activated, this exception will be thrown.
Cartalyst\Sentry\Users\UserNotFoundException         | If the provided user was not found, this exception will be thrown.

### Reset a User Password

In this section you will learn how easy it is to reset a user password with Sentry 2.

#### Step 1

The first step is to get a password reset code, to do this we use the
`getResetPasswordCode()` method.

##### Example

	try
	{
		// Find the user using the user email address
		$user = Sentry::findUserByLogin('john.doe@example.com');

		// Get the password reset code
		$resetCode = $user->getResetPasswordCode();

		// Now you can send this code to your user via email for example.
	}
	catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
	{
		echo 'User was not found.';
	}

#### Step 2

After your user received the password reset code you need to provide a way for them to validate that code, and reset their password.

All the logic part on how you pass the reset password code is all up to you.

#### Example

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Check if the reset password code is valid
	if ($user->checkResetPasswordCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb'))
	{
		// Attempt to reset the user password
		if ($user->attemptResetPassword('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb', 'new_password'))
		{
			// Password reset passed
		}
		else
		{
			// Password reset failed
		}
	}
	else
	{
		// The provided password reset code is Invalid
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                     | Description
--------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\UserNotFoundException  | If the provided user was not found, this exception will be thrown.

### Finding Users

Finding users can sometimes be difficult and harsh, well, Sentry provides you simple methods to find your users.

#### Get the Current Logged in User

Returns the user that's set with Sentry, does not check if a user is logged in or not. To do that, use `check()` instead.

```php
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
```

#### Find all the Users

This will return all the users.

```php
$users = Sentry::findAllUsers();
```

#### Find all the Users with access to a permissions(s)

Finds all users with access to a permission(s).

```php
// Feel free to pass a string for just one permission instead
$users = Sentry::findAllUsersWithAccess(array('admin', 'other'));
```

#### Find all the Users in a Group

Finds all users assigned to a group.

```php
$group = Sentry::findGroupByName('admin');

$users = Sentry::findAllUsersInGroup($group);
```

#### Find a User by their Credentials

Find a user by an array of credentials, which must include the login column. Hashed fields will be hashed and checked against their value in the database.

```php
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
```

#### Find a User by their Id

Find a user by their ID.

```php
try
{
	$user = Sentry::findUserById(1);
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Find a User by their Login Id

Find a user by their login ID.

```php
try
{
	$user = Sentry::findUserByLogin('john.doe@example.com');
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Find a User by their Activation Code

Find a user by their registration activation code.

```php
try
{
	$user = Sentry::findUserByActivationCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb');
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Find a User by their Reset Password Code

Find a user by their reset password code.

```php
try
{
	$user = Sentry::findUserByResetPasswordCode('8f1Z7wA4uVt7VemBpGSfaoI9mcjdEwtK8elCnQOb');
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                    | Description
-------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\UserNotFoundException | If the provided user was not found, this exception will be thrown.

### Helpers

#### checkPassword()

Checks if the provided password matches the user's current password.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	if($user->checkPassword('mypassword'))
	{
		echo 'Password matches.';
	}
	else
	{
		echo 'Password does not match.';
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### getGroups()

Returns the user groups.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserByID(1);

	// Get the user groups
	$groups = $user->getGroups();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### getPermissions()

Returns the user permissions.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserByID(1);

	// Get the user permissions
	$permissions = $user->getPermissions();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### getMergedPermissions()

Returns an array of merged permissions from groups and the user permissions.

```php
try
{
	// Find the user using the user id
	$user = Sentry::getUserProvider()->findById(1);

	// Get the user permissions
	$permissions = $user->getMergedPermissions();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### hasAccess($permission)

Checks to see if a user been granted a certain permission. This includes any
permissions given to them by groups they may be apart of as well. Users may
also have permissions with a value of '-1'. This value is used to deny users of
permissions that may have been assigned to them from a group.

Any user with `superuser` permissions automatically has access to everything,
regardless of the user permissions and group permissions.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserByID(1);

	// Check if the user has the 'admin' permission. Also,
	// multiple permissions may be used by passing an array
	if ($user->hasAccess('admin'))
	{
		// User has access to the given permission
	}
	else
	{
		// User does not have access to the given permission
	}
}
catch (Cartalyst\Sentry\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### hasAnyAccess($permissions)

This method calls the `hasAccess()` method, and it is used to check if an user
has access to any of the provided permissions.

If one of the provided permissions is found it will return `true` even though the
user may not have access to the other provided permissions.

```php
try
{
	// Find the user using the user id
	$user = Sentry::getUserProvider()->findById(1);

	// Check if the user has the 'admin' and 'foo' permission.
	if ($user->hasAnyAccess(array('admin', 'foo')))
	{
		// User has access to one of the given permissions
	}
	else
	{
		// User does not have access to any of the given permissions
	}
}
catch (Cartalyst\Sentry\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### isActivated()

Checks if a user is activated.

```php
try
{
	// Find the user
	$user = Sentry::findUserByLogin('jonh.doe@example.com');

	// Check if the user is activated or not
	if ($user->isActivated())
	{
		// User is Activated
	}
	else
	{
		// User is Not Activated
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### isSuperUser()

Returns if the user is a super user, it means, that has access to everything regardless of permissions.

```php
try
{
	// Find the user
	$user = Sentry::findUserByLogin('jonh.doe@example.com');

	// Check if this user is a super user
	if ($user->isSuperUser())
	{
		// User is a super user
	}
	else
	{
		// User is not a super user
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### inGroup($group)

Checks if a user is in a certain group.

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserByID(1);

	// Find the Administrator group
	$admin = Sentry::findGroupByName('Administrator');

	// Check if the user is in the administrator group
	if ($user->inGroup($admin))
	{
		// User is in Administrator group
	}
	else
	{
		// User is not in Administrator group
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```
