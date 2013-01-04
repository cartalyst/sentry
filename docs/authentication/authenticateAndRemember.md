<a id="authenticateAndRemember"></a>
###authenticateAndRemember($credentials)

----------

Authenticate and Remember a user based on credentials. This is a helper function for authenticate() which sets the `$remember` flag to true so the user is remembered (using a cookie). This is the "remember me" you are used to seeing on sites.

Parameters                   | Type            | Default       | Description
:--------------------------- | :-------------: | :------------ | :--------------
`$credentials` (required)    | array           | none          | An array of user fields to validate and login a user by. The Login field is required, all other fields are optional.

See `authenticate()` for more information.