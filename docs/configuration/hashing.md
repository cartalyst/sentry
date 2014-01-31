## Hashing

By default, Sentry encourages the sole use of the native PHP 5.5 hashing standard, `password_hash()`. Sentry requires no configuration to use this method.

While it is not encouraged for security reasons, we provide functionality to override the hashing strategy used by Sentry so as to accomodate for legacy applications moving forward.

There are 5 built in hashers:

1. [Native hasher](#native-hasher)
2. [Bcrypt hasher](#bcrypt-hasher)
3. [Callback hasher](#callback-hasher)
4. [Whirlpool hasher](#other-hashers)
5. [SHA256 hasher](#other-hashers)

### Native Hasher {#native-hasher}

The encouraged hasher to use in Sentry is the native hasher. It will use PHP 5.5's `password_hash()` function and is setup to use the most secure hashing strategy of the day (which is current bcrypt). There is no setup required for this hasher.

### Bcrypt Hasher {#bcrypt-hasher}

The Bcrypt hasher uses the Bcrypt hashing algorithm. It is a safe algorithm to use, however this hasher has been depreciated in favor of the native hasher as it provides a uniform API to whatever the chosen hashing strategy of the day is.

To use the Bcrypt hasher:

	// Native PHP
	$sentry->setHasher(new Cartalyst\Sentry\Hashing\BcryptHasher);

	// In Laravel
	Sentry::setHasher(new Cartalyst\Sentry\Hashing\BcryptHasher);

### Callback Hasher {#callback-hasher}

The callback hasher is a strategy which allows you to define the methods used to hash a value and in-turn check the hashed value. This is particularly useful when upgrading from legacy systems, which may use one or more hashing strategies. It will allow you to write logic that accounts for old strategies and new strategies, as seen in the example below.

Be **extremely** careful that you don't expose vunerabilities in your system by designing a hashing strategy that is unsafe to use.

To use the callback hasher:

	$hasher = function($value)
	{
		return password_hash($value, PASSWORD_DEFAULT);
	};

	$checker = function($value, $hashedValue)
	{
		// Try use the safe password_hash() function first, as all newly hashed passwords will use this
		if (password_verify($value, $hashedValue))
		{
			return true;
		}

		// Because we're upgrading from a legacy system, we'll check if the hash is an old one and therefore allow us to log the person in anyway
		return some_method_to_check_a_hash($value, $hashedValue);
	}

	// Native PHP
	$sentry->setHasher(new Cartalyst\Sentry\Hashing\NativeHasher($hasher, $checker));

	// In Laravel
	Sentry::setHasher(new Cartalyst\Sentry\Hashing\NativeHasher($hasher, $checker));

### Other Hashers {#other-hashers}

Other hashers, such as the **whirlpool hasher** and the **SHA256 hasher** are supported by Sentry, however we do not encourage their use as these algorithms are open to vunerabilities. We would encourage people to use the [callback hahser](#callback-hasher) and implement their own logic for moving away from such systems.

We understand that not every system needs to move away from these strategies however. Telling Sentry to use these strategies is straight forward:

	// Native PHP
	$sentry->setHasher(new Cartalyst\Sentry\Hashing\WhirlpoolHasher);
	$sentry->setHasher(new Cartalyst\Sentry\Hashing\Sha256Hasher);

	// In Laravel
	Sentry::setHasher(new Cartalyst\Sentry\Hashing\WhirlpoolHasher);
	Sentry::setHasher(new Cartalyst\Sentry\Hashing\Sha256Hasher);
