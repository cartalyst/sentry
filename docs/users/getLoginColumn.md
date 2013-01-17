<a id="getLoginColumn"></a>
###getLoginColumn()

----------

Gets the login column for the user.

`returns` string

####Example

	$emptyUser   = Sentry::getUserProvider()->getEmptyUser();
	$loginColumn = $emptyUser->getLoginColumn();
