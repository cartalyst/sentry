<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstall extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('email');
			$table->string('password');
			$table->string('reset_password_hash')->nullable();
			$table->string('activation_hash')->nullable();
			$table->boolean('activated')->default(1);
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('permissions')->nullable();
			$table->timestamps();
		});

		Schema::create('groups', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('permissions')->nullable();
			$table->timestamps();
		});

		Schema::create('user_group', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('group_id');
		});

		Schema::create('throttle', function($table)
		{
			$table->increments('id');
			$table->string('login');
			$table->integer('attempts');
			$table->boolean('suspended');
			$table->boolean('banned');
			$table->timestamp('last_attempt_at');
			$table->timestamp('suspended_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
		Schema::drop('groups');
		Schema::drop('user_group');
		Schema::drop('throttle');
	}

}