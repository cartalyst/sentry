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

### classes/user.php
-Sentry::user()->update_permissions

	$add_permissions = array(
		'blog_admin_delete'    => 0, // 0 - removes a rule from the permissions when merged
		'product_admin_delete' => 1  // 1 - will add a special permission for this user only
	);
	Sentry::user->update_permissions($add_permissions);

-Sentry::user()->permissions

	// will return the user's current merged permissions
	$current_permissions = Sentry::user()->permissions()

### classes/user.php
-Sentry::group()->update_permissions

	$add_permissions = array(
		'blog_admin_create' => 1, // 1 - will add a special permission for this user only
		'blog_admin_delete' => 1
		'blog_admin_delete' => 1,
		'blog_admin_delete' => 1,

		// remove blog_admin_delete_all
		'blog_admin_delete_all' => 0 // 0 - will remove a rule from the group's permissions
	);

	Sentry::group('group_name_or_id')->update_permissions($add_permissions);

-Sentry::group()->permissions

	// will return the user's current merged permissions
	$group_permissions = Sentry::group('groupname_or_id')->permissions()
