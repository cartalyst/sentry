<a id="enable"></a>
###enable()

----------

Enables throttling feature. Can be done on the throttle provider (global) level or on a throttle instance itself.

####Example

	$provider = Sentry::getThrottleProvider();
	$provider->enable();