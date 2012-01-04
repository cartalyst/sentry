<?php

return array(
	/** General Exception Messages **/
	'account_not_activated'  => 'User has not activated their account.',
	'account_is_disabled'    => 'This account has been disabled.',
	'invalid_limit_attempts' => 'Sentry Config Item: "limit.attempts" must be an integer greater than 0',
	'invalid_limit_time'     => 'Sentry Config Item: "limit.time" must be an integer greater than 0',
	'login_column_empty'     => 'You must set "login_column" in the Sentry config.',

	/** Group Exception Messages **/
	'group_already_exists'      => 'The group name ":group" already exists.',
	'group_level_empty'         => 'You must specify a level of the group.',
	'group_name_empty'          => 'You must specify a name of the group.',
	'group_not_found'           => 'Group ":group" does not exist.',
	'invalid_group_id'          => 'Group ID must be a valid integer greater than 0.',
	'not_found_in_group_object' => '":field" does not exist in "group" object.',
	'no_group_selected'         => 'No group is selected to get from.',
	'user_already_in_group'     => 'The User is already in group ":group".',
	'user_not_in_group'         => 'The User is not in group ":group".',

	/** User Exception Messages **/
	'column_already_exists'           => 'That :column already exists.',
	'column_and_password_empty'       => ':column and Password can not be empty.',
	'column_email_and_password_empty' => ':column, Email and Password can not be empty.',
	'column_is_empty'                 => ':column must not be empty.',
	'email_already_in_use'            => 'That email is already in use.',
	'invalid_old_password'            => 'Old password is invalid',
	'invalid_user_id'                 => 'User ID must be a valid integer greater than 0.',
	'no_user_selected'                => 'You must first select a user.',
	'no_user_selected_to_delete'      => 'No user is selected to delete.',
	'no_user_selected_to_get'         => 'No user is selected to get.',
	'not_found_in_user_object'        => '":field" does not exist in "user" object.',
	'password_empty'                  => 'Password can not be empty.',
	'user_already_enabled'            => 'The user is already enabled',
	'user_already_disabled'           => 'The user is already disabled',
	'user_not_found'                  => 'The user does not exist.',

	/** Attempts Exception Messages **/
    'login_ip_required'    => 'Login Id and IP Adress are required to add a login attempt.',
    'single_user_required' => 'Attempts can only be added to a single user, an array was given.',
    'user_suspended'       => 'You have been suspended from trying to login into account ":account" for :time minutes.',
);
