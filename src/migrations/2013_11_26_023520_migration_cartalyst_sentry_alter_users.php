<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryAlterUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('persist_code');
			$table->text('persistence_codes')->after('password')->nullable();
		});

		$users = DB::table('users')->get();

		foreach ($users as $user)
		{
			$permissions = array();

			if ($user->permissions)
			{
				foreach (json_decode($user->permissions) as $key => $value)
				{
					$permissions[$key] = ($value == 1);
				}
			}

			DB::table('users')
				->update(array(
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				));
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('persistence_codes');
			$table->string('persist_code')->nullable();
		});

		$users = DB::table('users')->get();

		foreach ($users as $user)
		{
			$permissions = array();

			if ($user->permissions)
			{
				foreach (json_decode($user->permissions) as $key => $value)
				{
					$permissions[$key] = ($value == true) ? 1 : -1;
				}
			}

			DB::table('users')
				->update(array(
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				));
		}
	}

}
