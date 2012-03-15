<?php

return array(
	/** General Exception Messages **/
	'account_not_activated'  => 'Utilizador não ativou a conta.',
	'account_is_disabled'    => 'Esta conta foi desativada.',

	// these should be caught in development so will leave them in English
	'invalid_limit_attempts' => 'Sentry Config Item: "limit.attempts" must be an integer greater than 0',
	'invalid_limit_time'     => 'Sentry Config Item: "limit.time" must be an integer greater than 0',
	'login_column_empty'     => 'You must set "login_column" in the Sentry config.',

	/** Group Exception Messages **/
	'group_already_exists'      => 'O grupo ":group" já existe.',
	'group_name_empty'          => 'Obrigatório definir um nome para o grupo.',
	'group_not_found'           => 'O grupo ":group" não existe.',
	'invalid_group_id'          => 'O ID do grupo tem que ser um valor inteiro maior que 0.',
	'not_found_in_group_object' => '":field" não existe no objeto "grupo".',
	'no_group_selected'         => 'Nenhum grupo foi selecionado.',
	'user_already_in_group'     => 'O Utilizador já faz parte do grupo ":group".',
	'user_not_in_group'         => 'O Utilizador não faz parte do grupo ":group".',

	/** User Exception Messages **/
	'column_already_exists'           => 'O campo :column já existe.',
	'column_and_password_empty'       => 'Os campos :column e Password não podem ser vazios.',
	'column_email_and_password_empty' => 'Os campos :column, Email e Password não podem ser vazios.',
	'column_is_empty'                 => 'O campo :column não pode ser vazio.',
	'email_already_in_use'            => 'O email já está a ser utilizado.',
	'invalid_old_password'            => 'Password antiga inválida.',
	'invalid_user_id'                 => 'O ID do Utilizador tem que ser um valor inteiro maior que 0.',
	'no_user_selected'                => 'Selecione um utilizador.',
	'no_user_selected_to_delete'      => 'Nenhum utilizador foi selecionado para ser eliminado.',
	'no_user_selected_to_get'         => 'Nenhum utilizador foi selecionado.',
	'not_found_in_user_object'        => '":field" não existe no objeto "utilizador".',
	'password_empty'                  => 'Password não pode ser vazia.',
	'user_already_enabled'            => 'O utilizador já está ativo.',
	'user_already_disabled'           => 'O utilizador já está inativo.',
	'user_not_found'                  => 'O utilizador não existe.',
	'username_already_in_use'         => 'O username já está em uso.',

	/** Attempts Exception Messages **/
	'login_ip_required'    => 'Login Id e IP Adress são obrigatórios para adicionar um tentativa de login.',
	'single_user_required' => 'Tentativas de login só podem ser adicionadas a um utilizador e foi fornecido um conjunto (array).',
	'user_suspended'       => 'A sua conta está suspensa por tentar fazer login na conta ":account" por :time minutos.',

	/** Permissions Messages **/
	'no_rules_added'    => 'Oops, não foram fornecidas regras de acesso para serem adicionadas.',
	'rule_not_found'    => 'A regra :rule, não existe na configuração. Por favor confirme a configuração do pacote Sentry.',
	'permission_denied' => 'Oops, não tem acesso ao recurso que tentou aceder.  ( :resource )'

);
 
 