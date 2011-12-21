<?php

return array(
	/** General Exception Messages **/
	'login_column_empty'        => 'You must set "login_column" in the Sentry config.',
        'account_not_activated'     => 'User has not activated their account.',
	'account_is_disabled'       => 'This account has been disabled.',
        'invalid_limit_attempts'    => 'Sentry Config Item: "limit.attempts" must be an integer greater than 0',
        'invalid_limit_time'        => 'Sentry Config Item: "limit.time" must be an integer greater than 0',

	/** Group Exception Messages **/
        'user_already_in_group'     => 'The User is already in group ":group".',
        'user_not_in_group'         => 'The User isn\'t in group ":group".',
        'invalid_group_id'          => 'Group ID must be a valid integer greater than 0.',
        'group_not_found'           => 'Group ":group" does not exist.',
        'group_level_empty'         => 'You must specify a level of the group.',
        'group_name_empty'          => 'You must specify a name of the group.',
        'no_group_selected'         => 'No group is selected to get from.',
        'not_found_in_group_object' => '":field" does not exist in "group" object.',

	/** User Exception Messages **/
        'invalid_user_id'                   => 'User ID must be a valid integer greater than 0.',
        'invalid_old_password'              => 'Old password is invalid',
        'user_not_found'                    => 'The user does not exist.',
        'not_found_in_user_object'          => '":field" does not exist in "user" object.',
        'password_empty'                    => 'Password can not be empty.',
        'column_and_password_empty'         => ':column and Password can not be empty.',
        'column_email_and_password_empty'   => ':column, Email and Password can not be empty.',
        'column_already_exists'             => 'That :column already exists.',
        'column_is_empty'                   => ':column must not be empty.',
        'email_already_in_use'              => 'That email is already in use.',
        'no_user_selected'                  => 'You must first select a user.',
        'no_user_selected_to_delete'        => 'No user is selected to delete.',
        'no_user_selected_to_get'           => 'No user is selected to get.',

	/** Attempts Exception Messages **/
        'user_suspended'    => 'You have been suspended from trying to login into account ":account" for :time minutes.',
);