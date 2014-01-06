<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryAlterThrottle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('throttle')->truncate();

		Schema::table('throttle', function(Blueprint $table)
		{
			$table->dropColumn('user_id');
			$table->dropColumn('attempts');
			$table->dropColumn('suspended');
			$table->dropColumn('banned');
			$table->dropColumn('last_attempt_at');
			$table->dropColumn('suspended_at');
			$table->dropColumn('banned_at');
			$table->dropColumn('ip_address');
		});

		Schema::table('throttle', function(Blueprint $table)
		{
			$table->integer('user_id')->after('id')->nullable();

			$table->string('type')->after('user_id');
			$table->string('ip')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('throttle')->truncate();

		Schema::table('throttle', function(Blueprint $table)
		{
			$table->dropColumn('type');
			$table->dropColumn('ip');
			$table->string('ip_address')->nullable();
			$table->dropTimestamps();

			$table->integer('attempts')->default(0);
			$table->boolean('suspended')->default(0);
			$table->boolean('banned')->default(0);
			$table->timestamp('last_attempt_at')->nullable();
			$table->timestamp('suspended_at')->nullable();
			$table->timestamp('banned_at')->nullable();

			$table->index('user_id');
		});
	}

}
