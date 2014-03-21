<?php
/**
 * Part of the Sentry package.
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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

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
			$table->string('slug')->after('id')->default('');
			$table->dropUnique('groups_name_unique');
		});

		$groups = DB::table('groups')->get();

		foreach ($groups as $group)
		{
			$permissions = [];

			if ($group->permissions)
			{
				foreach (json_decode($group->permissions) as $key => $value)
				{
					$permissions[$key] = (bool) $value;
				}
			}

			DB::table('groups')
				->where('id', $group->id)
				->update([
					'slug' => Str::slug($group->name),
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				]);
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
			$table->unique('name');
		});

		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropColumn('slug');
		});

		$groups = DB::table('groups')->get();

		foreach ($groups as $group)
		{
			$permissions = [];

			if ($group->permissions)
			{
				foreach (json_decode($group->permissions) as $key => $value)
				{
					$permissions[$key] = (int) $value;
				}
			}

			DB::table('groups')
				->where('id', $group->id)
				->update([
					'permissions' => (count($permissions) > 0) ? json_encode($permissions) : '',
				]);
		}
	}

}
