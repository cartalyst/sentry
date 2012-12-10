<a id="isSuspended"></a>
###isSuspended($login)

----------

Checks to see if the login is suspended.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::throttle()->isSuspended('test@test.com');