<a id="ban"></a>
###ban($login)

----------

Bans a login until specified otherwise with unban().

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::throttle()->ban('test@test.com');