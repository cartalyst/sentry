# Sentry 2.1

After the introduction of features like ACL (permissions) in 2.0, the new 2.1 version extends this by bringing you support for guest permissions.

##Features

* Authentication (via username or email)
* Authorization
* Groups
* ACL (Permissions)
	- Group Level or custom per user
	- Put guests in a dedicated group, if wished
* Remember Me
* User Suspension / Login Attempt Limiter
* Password Reset
* User Activation
* User Metadata

##Removed From v1.1
Before migrating from 1.1 to Sentry 2.0, please take note of the following features that have been removed and/or changed.

* Nested Groups are no longer supported
* is_admin for groups are no longer supported
* Group levels are no longer supported.

**Running the migration file will remove the support for the features above. Make sure you test this before running on a production server.**

If you migrate from 2.0 to 2.1 you will have an easy life, since no features have been removed.

##Added features
** 2.0 **
Get excited! Sentry 2.0 introduces ACL into the mix. Super simple to understand and implement.  We've tried to keep this as simple and quick as possible.

** 2.1 **
In 2.1 we extend our solid ACL system with the following features:

* Guests can be put in a group specified in the config file

##Downloading Sentry 2.0
You can download Sentry 2.0 into your FuelPHP's packages directory. You can download the latest version of Sentry via [zip here](https://github.com/cartalyst/sentry/tree/2.0/develop) or pull directly from the repository with the following command within the 'fuel/packages/' directory.

	$ git clone -b 2.0/develop git@github.com:cartalyst/sentry.git sentry

##Installing Sentry 2.0
Once downloaded, you may want to add sentry to the always load packages array in 'app/config.php'. Installing the tables is as simple as running an oil migration.

**Note:** *Your database must be setup first!*

From your command line run:

	$ php oil r migrate --packages=sentry

##What Next?
This is just a guide to get you started. Once Sentry 2.0 reaches final release we will have full documentation for you. Below we will describe how to use the new features in Sentry 2.0.
For features that are not 2.0 specific, please go see the [Sentry 1.1 Manual](http://sentry.cartalyst.com/manual/v1.1.html). Please note the features above that are no longer available.

##Using the New Features in Sentry 2.0
Here is a quick rundown on the new features in Sentry 2.0

###The Config File
You'll notice a new "permissions" section in the config file. Here is a glimpes of the new config section.

	'permissions' => array(
		/**
		  * enable permissions - true or false
		  */
		'enabled' => true,

		/**
          * super user - string
          * this will be used for the group and rules
          * if you change this, you need to make sure you change the
          */
		'superuser' => 'superuser',

		/**
	      * setup rules for permissions
	      * These are resources that will require access permissions.
	      * Rules are assigned to groups or specific users in the
	      * format module_controller_method or controller_method
	      *
	      * some samples are included below.
	      */
		'rules' => array(
			// page controller
			'page_edit',
			'page_save',

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

###classes/user.php
-Sentry::user()->update_permissions()

	$add_permissions = array(
		'blog_admin_delete'    => 0, // 0 - removes a rule from the permissions when merged
		'product_admin_delete' => 1  // 1 - will add a special permission for this user only
	);
	Sentry::user()->update_permissions($add_permissions);

-Sentry::user()->permissions()

	// will return the group's permissions in JSON format. This is mostly for UI usage. You can use FuelPHP's Format class
    // to easily convert it to any format thar you need.
	$current_permissions = Sentry::user()->permissions()

-Sentry::user()->merged_permissions()

	// will return the user's merged permissions in an array
	$merged_permissions = Sentry::user()->merged_permissions()

###classes/group.php
-Sentry::group()->update_permissions()

	$add_permissions = array(
		'blog_admin_create' => 1, // 1 - will add a special permission for this user only
		'blog_admin_delete' => 1
		'blog_admin_delete' => 1,
		'blog_admin_delete' => 1,

		// remove blog_admin_delete_all
		'blog_admin_delete_all' => 0 // 0 - will remove a rule from the group's permissions
	);

	Sentry::group('group_name_or_id')->update_permissions($add_permissions);

-Sentry::group('group_name_or_id')->permissions

	// will return the group's permissions in JSON format. This is mostly for UI usage. You can use FuelPHP's Format class
	// to easily convert it to any format thar you need.
	$group_permissions = Sentry::group('groupname_or_id')->permissions()
