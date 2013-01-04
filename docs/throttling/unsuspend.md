<a id="unsuspend"></a>
###unsuspend($login)

----------

Unsuspends a login. This also clears all previous attempts by the specified login if they were suspended.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::getThrottleProvider()->unsuspend('test@test.com');