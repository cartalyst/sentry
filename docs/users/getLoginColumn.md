<a id="getLoginColumn"></a>
###getLoginCOlumn()

----------

Gets the login column for the user.

`returns` string

####Example
	
	$emptyUser   = Sentry::getUserProvider()->getEmptyUser();
	$loginColumn = $emptyUser->getLoginColumn();