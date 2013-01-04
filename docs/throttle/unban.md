<a id="unban"></a>
###unban($login)

----------

Unbans a login.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::getThrottleProvider()->unban('test@test.com');