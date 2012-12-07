<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallUsersGroupsPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_groups', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('group_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_groups');
	}

}