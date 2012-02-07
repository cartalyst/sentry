<?php

return array(
	/** General Exception Messages **/
	'login_column_empty'     => 'Die Variable "login_column" muss in der Sentry Config Datei gesetzt sein.',
	'account_not_activated'  => 'Der Benutzeraccount wurde noch nicht aktiviert.',
	'account_is_disabled'    => 'Dieser Account wurde deaktiviert.',
	'invalid_limit_attempts' => 'Sentry Config: "limit.attempts" muss ein Integer gr&ouml;&szlig;er 0 sein',
	'invalid_limit_time'     => 'Sentry Config: "limit.time" muss ein Integer gr&ouml;&szlig;er 0 sein',

	/** Group Exception Messages **/
	'user_already_in_group'     => 'Der Benutzer ist bereits in Gruppe ":group" enthalten.',
	'group_already_exists'      => 'Die Gruppe ":group" existiert bereits.',
	'user_not_in_group'         => 'Der Benutzer ist nicht in der Gruppe ":group" enthalten.',
	'invalid_group_id'          => 'Die Gruppen-ID muss ein Integer gr&ouml;&szlig;er 0 sein.',
	'group_not_found'           => 'Die Gruppe ":group" existiert nicht.',
	'group_level_empty'         => 'Es muss ein Gruppenlevel angegeben werden.',
	'group_name_empty'          => 'Es muss ein Gruppenname angegeben werden.',
	'no_group_selected'         => 'Es wurde keine Gruppe ausgew&auml;hlt.',
	'not_found_in_group_object' => 'Das Feld ":field" existiert nicht im Objekt "Gruppe".',

	/** User Exception Messages **/
	'invalid_user_id'                 => 'Die User-ID muss ein Integer gr&ouml;&szlig;er 0 sein.',
	'invalid_old_password'            => 'Das alte Passwort ist falsch',
	'user_not_found'                  => 'Der Benutzer existiert nicht.',
	'not_found_in_user_object'        => 'Das Feld ":field" existiert nicht im Objekt "Benutzer".',
	'password_empty'                  => 'Das Passwort darf nicht leer sein.',
	'column_and_password_empty'       => ':column und Passwort d&uuml;rfen nicht leer sein.',
	'column_email_and_password_empty' => ':column, Email und Passwort d&uuml;rfen nicht leer sein.',
	'column_already_exists'           => 'Die Spalte :column ist bereits vorhanden.',
	'column_is_empty'                 => 'Die Spalte :column darf nicht leer sein.',
	'email_already_in_use'            => 'Die Email-Adresse wird bereits verwendet.',
	'no_user_selected'                => 'Es muss zuerst ein Benutzer ausgew&auml;hlt werden.',
	'no_user_selected_to_delete'      => 'Es wurde kein Benutzer zum l&ouml;schen ausgew&auml;hlt.',
	'no_user_selected_to_get'         => 'Es wurde kein Benutzer ausgw&auml;hlt.',

	/** Attempts Exception Messages **/
    'user_suspended'       => 'Aufgrund zu h&auml;figer Fehlversuche bei der Anmeldung, wurde der Account ":account" f&uuml;r :time Minuten gesperrt.',
    'login_ip_required'    => 'Login-ID und IP Adresse werden ben&ouml;tigt.',
    'single_user_required' => 'Die Anzahl der Versuche kann nur einem einzelnen Benutzer zugeordnet werden; ein Array wurde aber &uuml;bergeben.',
);
