<a id="isBanned"></a>
###isBanned($login)

----------

Checks to see if the login is banned.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::throttle()->isBanned('test@test.com');