<a id="getAllWithAccess"></a>
###getAllWithAccess()

----------

Finds all users with access to a permission(s).

`returns` array

####Example

	// Feel free to pass a string for just one permission instead
	$users = Sentry::getUserProvider()->getAllWithAccess(array('admin', 'other'));
