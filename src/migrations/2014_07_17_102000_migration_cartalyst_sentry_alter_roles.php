<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryAlterRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('roles_resources')){
            Schema::create('roles_resources', function($table)
            {
                $table->increments('id');
                $table->integer('role_id')->unsigned();
                $table->integer('resource_id')->unsigned();

                $table->foreign('role_id')->references('id')->on('roles');
                $table->foreign('resource_id')->references('id')->on('resources');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('roles_resources')){
            Schema::drop('roles_resources');
        }

    }

}
