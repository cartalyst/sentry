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

### config/sentry.php
Added a permissions section that allows you to enable permissions and define custom rules.

`
'permissions' => array(
	/**
	 * enable permissions - true or false
	 */
	'enabled' => true,

	/**
	 * setup rules for permissions
	 * These are resources that will require access permissions.
	 * Rules are assigned to groups or specific users in the
	 * format module_controller_method or controller_method
	 */
	'rules' => array(
		// user module admin
		'user_admin_create',
		'user_admin_read',
		'user_admin_update',
		'user_admin_delete',

		// blog module admin
		'blog_admin_create',
		'blog_admin_read',
		'blog_admin_update',
		'blog_admin_delete',
	)
)
`

### classes/user.php
- Sentry::user()->update_permissions
	- allows you to add/remove permissions to the user permissions column

	`
	$add_permissions = array(
		'blog_admin_delete'    => 0, // 0 - removes a rule from the permissions when merged
		'product_admin_delete' => 1  // 1 - will add a special permission for this user only
	);

	Sentry::user->update_permissions($add_permissions);
	`

- Sentry::user()->permissions
	- gives you access to the json version of the currently merged permissions

### classes/groups.php
- Sentry::group()->update_permissions
	- allows you to add/remove permissions to the group permissions column

	`
    $add_permissions = array(
    	'blog_admin_create'    => 1, // 1 - adds a permission
    	'blog_admin_read'      => 1,
    	'blog_admin_update'    => 1,
    	'blog_admin_delete'    => 1,

    	'product_admin_delete' => 0 // 0 - removes a permission
    );

    Sentry::group('admin')->update_permissions($add_permissions);
    `

- Sentry::group()->permissions
	- give you the json version of the specified groups permissions
