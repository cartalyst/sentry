<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('permissions')->nullable();
			$table->timestamps();

			$table->unique('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('groups');
	}

}
