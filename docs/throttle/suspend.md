<a id="suspend"></a>
###suspend($login)

----------

Suspends a login temporarily. Length of the suspension is set by the driver or setSuspensionTime($minutes).

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$login`                     | string          | none          | Login identifier

`returns` bool

####Example

	Sentry::throttle()->suspend('test@test.com');