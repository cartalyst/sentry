<a id="addAttempt"></a>
###addAttempt($login)

----------

Add an attempt to a login

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::getThrottleProvider()->addAttmept('test@test.com');