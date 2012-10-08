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

class Sentry_Users_Nullable
{
	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::connection(Config::get('sentry::sentry.db_instance'))->pdo->query('ALTER TABLE '.Config::get('sentry::sentry.table.users').' MODIFY password_reset_hash varchar(200) null');
		DB::connection(Config::get('sentry::sentry.db_instance'))->pdo->query('ALTER TABLE '.Config::get('sentry::sentry.table.users').' MODIFY temp_password varchar(200) null');
		DB::connection(Config::get('sentry::sentry.db_instance'))->pdo->query('ALTER TABLE '.Config::get('sentry::sentry.table.users').' MODIFY remember_me varchar(200) null');
		DB::connection(Config::get('sentry::sentry.db_instance'))->pdo->query('ALTER TABLE '.Config::get('sentry::sentry.table.users').' MODIFY activation_hash varchar(200) null');
		DB::connection(Config::get('sentry::sentry.db_instance'))->pdo->query('ALTER TABLE '.Config::get('sentry::sentry.table.users').' MODIFY ip_address varchar(200) null');
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{

	}

}
