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
			$table->string('type')->after('id');
			$table->renameColumn('ip_address', 'ip');
			$table->timestamps();

			$table->dropColumn('attempts');
			$table->dropColumn('suspended');
			$table->dropColumn('banned');
			$table->dropColumn('last_attempt_at');
			$table->dropColumn('suspended_at');
			$table->dropColumn('banned_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('throttle', function(Blueprint $table)
		{
			$table->dropColumn('type');
			$table->renameColumn('ip', 'ip_address');
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
