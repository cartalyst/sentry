<a id="logout"></a>
###logout()

----------

The logout method logs the user out and destroys all Sentry sessions / cookies for the user.

This method does **not** fail or throw any Exceptions if there is no user logged in.

`returns` void

####Example

	// Logs the user out
	Sentry::logout();
