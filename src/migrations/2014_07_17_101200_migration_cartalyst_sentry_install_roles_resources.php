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
                $table->integer('role_o')->unsigned()->nullable();



                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->index('name');
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
