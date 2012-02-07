<?php

return array(
	/** General Exception Messages **/
	'login_column_empty'     => 'De waarde voor "login_column" is niet gedefinieerd in de Sentry configuratie.',
	'account_not_activated'  => 'Gebruiker heeft zijn account niet geactiveerd.',
	'account_is_disabled'    => 'Dit account is geblokeerd.',
	'invalid_limit_attempts' => 'Sentry Configuratie Item: "limit.attempts" moet een geheel getal groter dan 0 zijn',
	'invalid_limit_time'     => 'Sentry Configuratie Item: "limit.time" moet een geheel getal groter dan 0 zijn',

	/** Group Exception Messages **/
	'user_already_in_group'     => 'De gebruiker is a lid van de groep ":group".',
	'group_already_exists'      => 'De gebruikersgroep ":group" bestaat al.',
	'user_not_in_group'         => 'De gebruiker is geen lid van de groep ":group".',
	'invalid_group_id'          => 'Groep ID moet een geheel getal groter dan 0 zijn.',
	'group_not_found'           => 'De groep ":group" bestaat niet.',
	'group_level_empty'         => 'Het groepsniveau dient te worden gespecificeerd.',
	'group_name_empty'          => 'De naam van de groep dient te worden gespecificeerd.',
	'no_group_selected'         => 'Er is geen groep geselecteerd.',
	'not_found_in_group_object' => '":field" bestaat niet in het "groep" object.',

	/** User Exception Messages **/
	'invalid_user_id'                 => 'Gebruikers ID moet een geheel getal groter dan 0 zijn.',
	'invalid_old_password'            => 'Oud wachtwoord is niet correct',
	'user_not_found'                  => 'De gebruiker bestaat niet.',
	'not_found_in_user_object'        => '":field" bestaat niet in het "user" object.',
	'password_empty'                  => 'Het wachtwoord mag niet leeg zijn.',
	'column_and_password_empty'       => ':column en Wachtwoord mogen niet leeg zijn.',
	'column_email_and_password_empty' => ':column, Email en Wachtwoord mogen niet leeg zijn.',
	'column_already_exists'           => 'Die :column bestaat reeds.',
	'column_is_empty'                 => ':column mag niet leeg zijn.',
	'email_already_in_use'            => 'Dit email adres is reeds in gebruik.',
	'no_user_selected'                => 'Er moet eerst een gebruiker geselecteerd zijn.',
	'no_user_selected_to_delete'      => 'Er is geen gebruiker geselecteerd om te verwijderen.',
	'no_user_selected_to_get'         => 'Er is geen gebruiker geselecteerd om op te halen.',

	/** Attempts Exception Messages **/
    'user_suspended'       => 'You have been suspended from trying to login into account ":account" for :time minutes.',
    'login_ip_required'    => 'Login Id en IP Adres zijn verplicht bij registratie van een login poging.',
    'single_user_required' => 'Login pogingen kunnen alleen aan een enkele gebruiker worden toegewezen, een array is niet mogelijk.',
);
