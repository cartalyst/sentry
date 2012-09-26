<?php

return array(
	/** General Exception Messages **/
	'account_not_activated'  => 'L’utilisateur n’a pas confirmé son compte.',
	'account_is_disabled'    => 'Ce compte a été désactivé.',
	'invalid_limit_attempts' => 'L’élément de configuration Sentry : "limit.attempts" doit être un entier plus grand que 0',
	'invalid_limit_time'     => 'L’élément de configuration Sentry : "limit.time" doit être un entier plus grand que 0',
	'login_column_empty'     => 'Vous devez spécifier "login_column" dans la configuration Sentry.',

	/** Group Exception Messages **/
	'group_already_exists'      => 'Le nom du groupe ":group" existe déjà.',
	'group_level_empty'         => 'Vous devez spécifier le niveau du groupe.',
	'group_name_empty'          => 'Vous devez spécifier le nom du groupe.',
	'group_not_found'           => 'Le groupe ":group" n’existe pas.',
	'invalid_group_id'          => 'Le Group ID doit être un entier valide plus grand que 0.',
	'not_found_in_group_object' => '":field" n’existe pas dans l’objet "group".',
	'no_group_selected'         => 'Aucun groupe sélectionné.',
	'user_already_in_group'     => 'L’utilisateur est déjà dans le groupe ":group".',
	'user_not_in_group'         => 'L’utilisateur n’est pas dans le groupe ":group".',

	/** L’utilisateur Exception Messages **/
	'column_already_exists'           => 'Le champ :column existe déjà.',
	'column_and_password_empty'       => 'Les champs :column et Mot de passe ne peuvent être vides.',
	'column_email_and_password_empty' => 'Les champs :column, E-mail and Mot de passe ne peuvent être vides.',
	'column_is_empty'                 => 'Le champ :column ne peut pas être vide.',
	'email_already_in_use'            => 'Cet e-mail est déjà utilisé.',
	'invalid_old_password'            => 'L’ancien mot de passe est incorrect',
	'invalid_user_id'                 => 'L’ID d’utilisateur ID doit être un entier plus grand que 0.',
	'no_user_selected'                => 'Vous devez d’abord sélectionner un utilisateur.',
	'no_user_selected_to_delete'      => 'Aucun utilisateur sélectionné pour la suppression.',
	'no_user_selected_to_get'         => 'Aucun utilisateur sélectionné.',
	'not_found_in_user_object'        => 'Le champ ":field" n’existe pas dans l’objet "user".',
	'password_empty'                  => 'Le mot de passe ne peut pas être vide.',
	'user_already_enabled'            => 'L’utilisateur est déjà activé.',
	'user_already_disabled'           => 'L’utilisateur est déjà désactivé.',
	'user_not_found'                  => 'L’utilisateur n’existe pas.',
	'username_already_in_use'         => 'Cet identifiant est déjà utilisé.',

	/** Attempts Exception Messages **/
    'login_ip_required'    => 'L’ID d’identification et l’adresse IP sont requis pour ajouter une tentative d’identification.',
    'single_user_required' => 'Les tentatives ne peuvent être ajoutées qu’à un seul utilisateur, or un tableau a été donné.',
    'user_suspended'       => 'Vous êtes suspendu pour avoir essayé de vous identifier avec le compte ":account" pour :time minutes.',

    /** Hashing **/
    'hash_strategy_null'      => 'La stratégie de Hashing est vide ou nulle. Une stratégie de hashing doit être définie.',
    'hash_strategy_not_exist' => 'Le fichier de stratégie de Hashing n’existe pas.',

	/** Permissions Messages **/
	'no_rules_added'    => 'Oups, vous avez oublié de spécifier les règles à ajouter.',
	'rule_not_found'    => 'La règle :rule, n’existe pas dans vos règles configurées. Veuillez vérifier vos règles dans la configuration Sentry.',
	'permission_denied' => 'Oups, vous n’avez pas la permission pour accéder à :resource'

);
