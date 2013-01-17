<a id="isEnabled"></a>
###isEnabled()

----------

Checks to see the throttling feature is enabled, like `enable()` and `disable()`, this can be done globally or on an individual throttle instance.

`returns` boolean
`throws`  UserNotFoundException

####Example

	$provider = Sentry::getThrottleProvider();
	$enabled = $provider->isEnabled();
