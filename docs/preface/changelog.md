## Changelog

### 3.0

#### Hashing

We have removed the configuration option for setting a custom hashing strategy to discourage people using anything except the new standard, `password_hash*()` (the native PHP 5.5 hasher that has been backported to PHP 5.4+).

If you are running a legacy hashing strategy that you wish to keep using, you may override the hasher after Sentry has been bootstrapped. A good place to do this is in your application's start or bootstrap file.

	// Native PHP
	$sentry->setHasher(new Cartalyst\Sentry\Hashing\WhirlpoolHasher);

	// In Laravel
	Sentry::setHasher(new Cartalyst\Sentry\Hashing\WhirlpoolHasher);

Read more about available hashing strategies over at the [hashing configuration]({url}/cofiguration/hashing).
