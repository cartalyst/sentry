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

class Sentry_Database_Rules
{

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create rules table
		Schema::table(Config::get('sentry::sentry.table.rules'), function($table) {
			$table->on(Config::get('sentry::sentry.db_instance'));
			$table->create();
			$table->increments('id')->unsigned();
			$table->string('rule')->unique();
			$table->string('description')->nullable();
		});

		// Insert default values
		DB::connection(Config::get('sentry::sentry.db_instance'))
			->table(Config::get('sentry::sentry.table.rules'))
			->insert(array('rule' => 'superuser', 'description' => 'Access to Everything'));
		DB::connection(Config::get('sentry::sentry.db_instance'))
			->table(Config::get('sentry::sentry.table.rules'))
			->insert(array('rule' => 'is_admin', 'description' => 'Administrative Privileges'));
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop rules table
		Schema::table(Config::get('sentry::sentry.table.rules'), function($table) {
			$table->on(Config::get('sentry::sentry.db_instance'));
			$table->drop();
		});
	}

}
