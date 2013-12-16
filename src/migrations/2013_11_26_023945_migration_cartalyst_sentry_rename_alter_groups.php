<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryRenameAlterGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('groups', function(Blueprint $table)
		{
			$table->string('slug')->after('id');
			$table->dropUnique('groups_name_unique');
			$table->unique('slug');
		});

		$groups = DB::table('groups')->get();

		foreach ($groups as $group)
		{
			$permissions = array();

			if ($group->permissions)
			{
				foreach (json_decode($group->permissions) as $key => $value)
				{
					$permissions[$key] = (bool) $value;
				}
			}

			DB::table('groups')
				->update(array(
					'slug' => Str::slug($group->name),
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
		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropColumn('slug');
		});

		$groups = DB::table('groups')->get();

		foreach ($groups as $group)
		{
			$permissions = array();

			if ($group->permissions)
			{
				foreach (json_decode($group->permissions) as $key => $value)
				{
					$permissions[$key] = (int) $value;
				}
			}

			DB::table('groups')
				->update(array(
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				));
		}
	}

}
