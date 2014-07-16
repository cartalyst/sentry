<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystRenameUsersGroups extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('users_groups', 'users_roles');
        Schema::table('users_roles', function($table)
        {
            $table->renameColumn('group_id', 'role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('users_roles', 'users_groups');
        Schema::table('users_groups', function($table)
        {
            $table->renameColumn('role_id', 'group_id');
        });
    }

}
