<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryRenameAlterGroupsUsersPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('users_groups', 'groups_users');

		Schema::table('groups_users', function(Blueprint $table)
		{
			$table->timestamps();
		});

		$now = Carbon::now();

		DB::table('groups_users')->update(array(
			'created_at' => $now,
			'updated_at' => $now,
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups_users', function(Blueprint $table)
		{
			$table->dropTimestamps();
		});

		Schema::rename('groups_users', 'users_groups');
	}

}
