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
			$table->increments('id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->integer('group_id')->unsigned();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('no action')->onDelete('cascade');
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('no action')->onDelete('cascade');
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
