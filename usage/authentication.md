## Authentication

In this section we'll be covering how to authentication and logins.

### Sentry::authenticate()

Authenticates a user based on the provided credentials.

If the authentication is successful, password reset fields and any invalidauthentication attempts will be cleared.

Param        | Required | Default | Type  | Description
------------ | -------- | ------- | ----- | -----------------------------------
$credentials | true     | null    | array | Array that should contain the user credentials like `email` and `password`.
$remember    | false    | false   | bool  | Flag to wether Sentry should remember the user. It sets a Cookie.

#### Example

```php
try
{
	// Login credentials
	$credentials = array(
		'email'    => 'john.doe@example.com',
		'password' => 'password',
	);

	// Authenticate the user
	$user = Sentry::authenticate($credentials, false);
}
catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
{
	echo 'Login field is required.';
}
catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
{
	echo 'Password field is required.';
}
catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
{
	echo 'Wrong password, try again.';
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
{
	echo 'User is not activated.';
}

// The following is only required if the throttling is enabled
catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
{
	echo 'User is suspended.';
}
catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
{
	echo 'User is banned.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\LoginRequiredException      | When you don't provide the required `login` field, this exception will be thrown.
Cartalyst\Sentry\Users\PasswordRequiredException   | When you don't provide the `password` field, this exception will be thrown.
Cartalyst\Sentry\Users\UserNotActivatedException   | When the provided user is not activated, this exception will be thrown.
Cartalyst\Sentry\Users\UserNotFoundException       | If the provided user was not found, this exception will be thrown.
Cartalyst\Sentry\Users\WrongPasswordException      | When the provided password is incorrect, this exception will be thrown.
Cartalyst\Sentry\Throttling\UserSuspendedException | When the provided user is suspended, this exception will be thrown.
Cartalyst\Sentry\Throttling\UserBannedException    | When the provided user is banned, this exception will be thrown.

### Sentry::authenticateAndRemember()

Authenticates and Remembers a user based on credentials. This is an helper function for the `authenticate()` which sets the `$remember` flag to true so the user is remembered (using a cookie). This is the "remember me" you are used to see on web sites.

#### Example

	Sentry::authenticateAndRemember($credentials);

### Sentry::login()

Logs in the provided user and sets properties in the session.

If the login is successful, password reset fields and any invalid authentication attempts will be cleared.

Param        | Required | Default | Type   | Description
------------ | -------- | ------- | ------ | -----------------------------------
$user        | true     | null    | object | A user object `Cartalyst\Sentry\Users\UserInterface`.
$remember    | false    | false   | bool   | Flag to wether Sentry should remember the user. It sets a Cookie.

#### Example

```php
try
{
	// Find the user using the user id
	$user = Sentry::findUserById(1);

	// Log the user in
	Sentry::login($user, false);
}
catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
{
	echo 'Login field is required.';
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User not found.';
}
catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
{
	echo 'User not activated.';
}

// Following is only needed if throttle is enabled
catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
{
	$time = $throttle->getSuspensionTime();

	echo "User is suspended for [$time] minutes.";
}
catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
{
	echo 'User is banned.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Users\LoginRequiredException      | When you don't provide the required `login` field, this exception will be thrown.
Cartalyst\Sentry\Users\UserNotFoundException       | If the provided user was not found, this exception will be thrown.
Cartalyst\Sentry\Users\UserNotActivatedException   | When the provided user is not activated, this exception will be thrown.
Cartalyst\Sentry\Throttling\UserSuspendedException | When the provided user is suspended, this exception will be thrown.
Cartalyst\Sentry\Throttling\UserBannedException    | When the provided user is banned, this exception will be thrown.

### Sentry::loginAndRemember()

Logs in and Remembers a user based on credentials. This is an helper function for the `login()` which sets the `$remember` flag to true so the user is remembered (using a cookie). This is the "remember me" you are used to see on web sites.

#### Example

```php
Sentry::loginAndRemember($user);
```

### Sentry::logout()

The logout method logs the user out and destroys all Sentry sessions / cookies for the user.

This method does **not** fail or throw any Exceptions if there is no user logged in.

#### Example

```php
Sentry::logout();
```

### Sentry::check()

The check method returns a `bool` of whether the user is logged in or not, or if the user is not activated.

If it's logged in, the current User is set in Sentry so you can easily access it via `getUser()`.

A user must be activated to pass `check()`.

#### Example

```php
if ( ! Sentry::check())
{
	// User is not logged in, or is not activated
}
else
{
	// User is logged in
}
```
