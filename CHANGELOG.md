Sentry 2.0 Changelog
====================
Sentry 2.0 includes several changes to the way groups and permissions are handled, including the introduction of ACL.

Removed Since v1.1
------------------
- Removed nested sets
- Removed group is_admin
- Removed group level

Added in 2.0
------------
Get excited!  ACL (Permissions) have been added to Sentry 2.0!

### classes/user.php
- Sentry::user()->update_permissions
	- allows you to add/remove permissions to the user permissions column
- Sentry::user()->permissions
	- gives you access to the json version of the currently merged permissions

### classes/groups.php
- Sentry::group()->update_permissions
	- allows you to add/remove permissions to the group permissions column

- Sentry::group()->permissions
	- give you the json version of the specified groups permissions
