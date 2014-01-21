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
				->where('id', $group->id)
				->update(array(
					'slug' => Str::slug($group->name),
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				));
		}

		Schema::table('groups', function(Blueprint $table)
		{
			$table->unique('slug');
		});
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
			$table->dropUnique('groups_slug_unique');
			$table->dropColumn('slug');
			$table->unique('name');
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
				->where('id', $group->id)
				->update(array(
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				));
		}
	}

}
