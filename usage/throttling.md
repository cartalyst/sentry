## Throttle

### Disable the Throttling Feature

Disables the throttling feature.

Can be done on the throttle provider (global) level or on a throttle instance itself.

#### Example

```php
// Get the Throttle Provider
$throttleProvider = Sentry::getThrottleProvider();

// Disable the Throttling Feature
$throttleProvider->disable();
```

### Enable the Throttling Feature

Enables the throttling feature.

Can be done on the throttle provider (global) level or on a throttle instance itself.

#### Example

```php
// Get the Throttle Provider
$throttleProvider = Sentry::getThrottleProvider();

// Enable the Throttling Feature
$throttleProvider->enable();
```

### Check the Throttling Feature Status

Checks to see if the throttling feature is enabled or disabled.

#### Example

```php
// Get the Throttle Provider
$provider = Sentry::getThrottleProvider();

// Check if the Throttling feature is enabled or disabled
if($provider->isEnabled())
{
	// The Throttling Feature is Enabled
}
else
{
	// The Throttling Feature is Disabled
}
```

### User Throttling

#### Ban user(s)

Bans the user associated with the throttle.

```php
try
{
	// Find the user using the user id
	$throttle = Sentry::findThrottlerByUserId(1);

	// Ban the user
	$throttle->ban();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Unban user(s)

Unbans the user associated with the throttle.

```php
try
{
	// Find the user using the user id
	$throttle = Sentry::findThrottlerByUserId(1);

	// Unban the user
	$throttle->unBan();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Check if a User is Banned

Checks to see if the user is banned.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	if($banned = $throttle->isBanned())
	{
		// User is Banned
	}
	else
	{
		// User is not Banned
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Suspend user(s)

Suspends a user temporarily. Length of the suspension is set by the driver or setSuspensionTime($minutes).

```php
try
{
	// Find the user using the user id
	$throttle = Sentry::findThrottlerByUserId(1);

	// Suspend the user
	$throttle->suspend();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Unsuspend user(s)

Unsuspends a login. This also clears all previous attempts by the specified login if they were suspended.

```php
try
{
	// Find the user using the user id
	$throttle = Sentry::findThrottlerByUserId(1);

	// Unsuspend the user
	$throttle->unsuspend();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Check if a User is Suspended

Checks to see if the user is suspended.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	if($suspended = $throttle->isSuspended())
	{
		// User is Suspended
	}
	else
	{
		// User is not Suspended
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Set the User Suspension Time

Sets the length of the suspension.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$throttle->setSuspensionTime(10);
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Get the User Suspension Time

Retrieves the length of the suspension time set by the throttling driver.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$suspensionTime = $throttle->getSuspensionTime();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Add a Login Attempt

Adds an attempt to the throttle object.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$throttle->addLoginAttempt();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Get Login Attempts

Retrieves the number of attempts a user currently has tried. Checks suspension time to see if login attempts can be reset. This may happen if the suspension time was (for example) 10 minutes however the last login was 15 minutes ago, attempts will be reset to 0.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$attempts = $throttle->getLoginAttempts();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Clear Login Attempts

Clears all login attempts, it also unsuspends them. This does not unban a login.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$throttle->clearLoginAttempts();
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Check the User Throttle Status

Checks the login throttle status and throws a number of Exceptions upon failure.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	if ($throttle->check())
	{
		echo 'Good to go.';
	}
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
{
	$time = $throttle->getSuspensionTime();

	echo "User is suspended for [$time] minutes.";
}
catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
{
	ehco 'User is banned.';
}
```

#### Set Attempt Limit

Sets the number of attempts allowed before suspension.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$throttle->setAttemptLimit(3);
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Get Attempt Limit

Retrieves the number of attempts allowed by the throttle object.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);

	$attemptLimit = $throttle->getAttemptLimit();
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
Cartalyst\Sentry\Throttle\UserNotFoundException    | If the provided user was not found, this exception will be thrown.
Cartalyst\Sentry\Throttling\UserSuspendedException | When the provided user is suspended, this exception will be thrown.
Cartalyst\Sentry\Users\UserBannedException         | When the provided user is banned, this exception will be thrown.

### Find User(s)

#### Find a User by their Id

Retrieves a throttle object based on the user ID provided. Will always retrieve a throttle object.

```php
try
{
	$throttle = Sentry::findThrottlerByUserId(1);
}
catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
{
	echo 'User was not found.';
}
```

#### Find a User by their Login

Retrieves a throttle object based on the user login provided. Will always retrieve a throttle object.

```php
try
{
	$throttle = Sentry::findThrottlerByUserLogin('john.doe@example.com');
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
