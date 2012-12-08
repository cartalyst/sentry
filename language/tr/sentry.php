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
	'account_not_activated'           => 'Kullanıcı hesabını aktifleştirmedi.',
	'account_is_disabled'             => 'Bu hesap etkisizleştirilmiş.',
	'invalid_limit_attempts'          => 'Sentry Konfigürasyon Elemanı: "limit.attempts" sıfırdan büyük ve sayı olmalı',
	'invalid_limit_time'              => 'Sentry Konfigürasyon Elemanı: "limit.time" sıfırdan büyük ve sayı olmalı',
	'login_column_empty'              => 'Sentry Konfigürasyonunda "login_column" kurulmalı.',

	/** Group Exception Messages **/
	'group_already_exists'            => 'Grup adı ":group" zaten mevcut.',
	'group_level_empty'               => 'Gruba seviye belirleyiniz.',
	'group_name_empty'                => 'Gruba isim tanımlamalısın.',
	'group_not_found'                 => 'Grup ":group" mevcut değil.',
	'invalid_group_id'                => 'Grup ID sıfırdan büyük ve geçerli sayı olmalı.',
	'not_found_in_group_object'       => '":field" does not exist in "group" object.',
	'no_group_selected'               => 'Veri alınacak grup seçilmedi.',
	'user_already_in_group'           => 'Kullanıcı zaten ":group" grubunda.',
	'user_not_in_group'               => 'Kullanıcı ":group" grubunda değil.',

	/** User Exception Messages **/
	'column_already_exists'           => ':column zaten mevcut.',
	'column_and_password_empty'       => ':column ve Şifre boş bırakılamaz.',
	'column_email_and_password_empty' => ':column, E-posta ve şifre boş bırakılamaz.',
	'column_is_empty'                 => ':column boş bırakılmamalı.',
	'email_already_in_use'            => 'E-posta müsait değil.',
	'invalid_old_password'            => 'Eski şifre geçersiz',
	'invalid_user_id'                 => 'Kullanıcı ID sıfırdan büyük ve geçerli sayı olmalı.',
	'no_user_selected'                => 'Önce kullanıcı seçin.',
	'no_user_selected_to_delete'      => 'Silinecek kullanıcı seçilmedi.',
	'no_user_selected_to_get'         => 'Verisi alınacak kullanıcı seçilmedi.',
	'not_found_in_user_object'        => '":field" does not exist in "user" object.',
	'password_empty'                  => 'Şifre boş olamaz.',
	'user_already_enabled'            => 'Kullanıcı zaten aktif',
	'user_already_disabled'           => 'Kullanıcı zaten pasif',
	'user_not_found'                  => 'Kullanıcı mevcut değil.',
	'username_already_in_use'         => 'Kullanıcı adı müsait değil.',

	/** Attempts Exception Messages **/
	'login_ip_required'               => 'Giriş denemesi için giriş Id ve IP adresi gerekli.',
	'single_user_required'            => 'Denemeler sadece tek kullanıcıya eklenir, veri olarak dizi girdiniz.',
	'user_suspended'                  => 'Hesabınız ":account", :time dakika kadar donduruldu.',

	/** Hashing **/
	'hash_strategy_null'              => 'Hashing stratejji boş veya tanımsız. Hashing stratejisi tanımlı olmalı.',
	'hash_strategy_not_exist'         => 'Hashing strateji dosyası mevcut değil.',

	/** Permissions Messages **/
	'no_rules_added'                  => 'Dikkat! Kural eklemeyi unuttunuz.',
	'rule_not_found'                  => 'Kural :rule, kural konfigürasyonlarınızda bulunmuyor. Sentry Konfigürasyonlarını kontrol edin.',
	'permission_denied'               => 'Dikkat! :resource erişimine izniniz yok',

);
