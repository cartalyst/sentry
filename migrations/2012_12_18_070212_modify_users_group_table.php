<?php

class Sentry_Modify_Users_Group_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table(Config::get('sentry::sentry.table.users_groups'), function($table)
    		{
			$table->on(Config::get('sentry::sentry.db_instance'));
			$table->boolean('is_default');
		});
		//
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		 Schema::table(Config::get('sentry::sentry.table.users_groups'), function($table)
                {
                        $table->on(Config::get('sentry::sentry.db_instance'));
                        $table->drop_column('is_default');
                });

	}

}
