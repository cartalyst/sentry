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

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrationCartalystSentryInstallActivations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('code');
			$table->boolean('completed')->default(0);
			$table->timestamp('completed_at')->nullable();
			$table->timestamps();
		});

		$users = DB::table('users')->get();
		$now = Carbon::now();
		$format = DB::connection()->getQueryGrammar()->getDateFormat();

		foreach ($users as $user)
		{
			$data = [
				'user_id'      => $user->id,
				'code'         => (string) $user->activation_code,
				'completed'    => (int) $user->activated,
				'created_at'   => $now,
				'updated_at'   => $now,
			];

			if ($user->activated_at)
			{
				$data['completed_at'] = Carbon::createFromFormat($format, $user->activated_at);
			}

			DB::table('activations')
				->insert($data);
		}

		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn(array(
				'activated',
				'activation_code',
				'activated_at',
			));
		});
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
			$table->boolean('activated')->default(0);
			$table->string('activation_code')->nullable();
			$table->timestamp('activated_at')->nullable();
		});

		$activations = DB::table('activations')->get();
		$format = DB::connection()->getQueryGrammar()->getDateFormat();

		foreach ($activations as $activation)
		{
			$data = [
				'activation_code' => $activation->code,
				'activated' => $activation->completed,
			];

			if ($activation->completed_at)
			{
				$data['activated_at'] = Carbon::createFromFormat($format, $activation->completed_at);
			}

			DB::table('users')
				->where('id', $activation->user_id)
				->update($data);
		}

		Schema::drop('activations');
	}

}
