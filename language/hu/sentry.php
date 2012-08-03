<?php
/**
 * Part of the Sentry bundle for Laravel.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/** General Exception Messages **/
	'account_not_activated'  => 'A felhasználó még nem aktiválta a fiókját.',
	'account_is_disabled'    => 'Ez a fiók ki lett kapcsolva.',
	'invalid_limit_attempts' => 'A "limit.attempts" Sentry beállításnak 0-nál nagyobb számnak kell lennie',
	'invalid_limit_time'     => 'A "limit.time" Sentry beállításnak egy 0-nál nagyobb számnak kell lennie',
	'login_column_empty'     => 'Be kell állítanod a "login_column" értékét a Sentry konfigurációban.',

	/** Group Exception Messages **/
	'group_already_exists'      => 'A ":group" nevű csoport már létezik.',
	'group_level_empty'         => 'Meg kell adnod a csoport szintjét.',
	'group_name_empty'          => 'El kell nevezned a csoportot.',
	'group_not_found'           => 'A ":group" nevű csoport nem létezik.',
	'invalid_group_id'          => 'A Group ID 0-nál nagyobb szám kell legyen.',
	'not_found_in_group_object' => 'A ":field" mező nem létezik a "group" objektumban.',
	'no_group_selected'         => 'Nincs csoport kiválasztva.',
	'user_already_in_group'     => 'A felhasználó már a(z) ":group" csoport tagja.',
	'user_not_in_group'         => 'A felhasználó nem tagja a(z) ":group" csoportnak.',

	/** User Exception Messages **/
	'column_already_exists'           => 'A(z) :column már létezik.',
	'column_and_password_empty'       => 'A(z) :column és a jelszó nem hiányozhat.',
	'column_email_and_password_empty' => 'A(z) :column, Email és a Jelszó nem lehet hiányozhat.',
	'column_is_empty'                 => ':column nem lehet üres.',
	'email_already_in_use'            => 'A megadott email fiók már használatban van.',
	'invalid_old_password'            => 'A régi jelszó nem megfelelő',
	'invalid_user_id'                 => 'A User ID 0-nál nagyobb szám kell legyen.',
	'no_user_selected'                => 'Először ki kell választanod egy felhasználót.',
	'no_user_selected_to_delete'      => 'Nincs kiválasztva felhasználó a törlésre.',
	'no_user_selected_to_get'         => 'Nem található a keresett felhasználó .',
	'not_found_in_user_object'        => 'A(z) ":field" mező nem létezik a "user" objektumban.',
	'password_empty'                  => 'A jelszó nem maradhat üresen.',
	'user_already_enabled'            => 'A fiók már aktiválva van',
	'user_already_disabled'           => 'A felhasználó már ki van tiltva',
	'user_not_found'                  => 'A felhasználó nem létezik.',
	'username_already_in_use'         => 'A megadott felhasználónév már foglalt.',

	/** Attempts Exception Messages **/
    'login_ip_required'    => 'Belépési azonosító és IP cím szükséges a belépési kísérlet hozzáadásához.',
    'single_user_required' => 'Próbálkozás csak egy felhasználóhoz rendelődhet, tömb került feldolgozásra.',
    'user_suspended'       => 'Fel lettél függesztve a bejelentkezések alól a ":account" fióktól :time percig.',

    /** Hashing **/
    'hash_strategy_null'      => 'A Hashelési stratégia üres, vagy null. Kötelező egy stratégia kiválasztása.',
    'hash_strategy_not_exist' => 'Nem található a hashelési stratégiához tartozó file.',

	/** Permissions Messages **/
	'no_rules_added'    => 'Hoppá, elfelejtettél megadni szabályt.',
	'rule_not_found'    => 'A :rule szabály nincs a definiált szabályok közt. Kérlek ellenőrizd a beállítások a Sentry konfigurációs file-jában.',
	'permission_denied' => 'Nincs jogosoltságod hozzáférni a :resource -hoz',

);
