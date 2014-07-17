<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryAlterUsersRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users_roles')){
            Schema::table('users_roles', function($table)
            {
                $table->renameColumn('group_id', 'role_id');
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
        if (Schema::hasTable('users_roles')){
            Schema::table('users_roles', function($table)
            {
                $table->renameColumn('role_id', 'group_id');
            });
        }

    }

}
