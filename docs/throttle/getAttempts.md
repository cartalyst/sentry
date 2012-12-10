<a id="getAttempts"></a>
###getAttempts($login)

----------

Retrieves the number of attempts a login currently has tried.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` int

####Example

	Sentry::throttle()->getAttempts('test@test.com');