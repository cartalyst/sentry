<a id="clearAttempts"></a>
###clearAttempts($login)

----------

Clears all a logins attempts as well as unsuspending them. This does not unban a login.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::throttle()->clearAttempts('test@test.com');