<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallRolesResources extends Migration {

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
