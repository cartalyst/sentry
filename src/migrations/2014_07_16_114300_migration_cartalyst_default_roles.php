<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystDefaultRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users_roles')){
            DB::table('users_roles')->insert(
                array(
                    'name' => 'admin'
                )
            );

            DB::table('users_roles')->insert(
                array(
                    'name' => 'user'
                )
            );

            DB::table('users_roles')->insert(
                array(
                    'name' => 'quest'
                )
            );
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
            DB::table('users_roles')->where('name', 'admin')->delete();
            DB::table('users_roles')->where('name', 'user')->delete();
            DB::table('users_roles')->where('name', 'quest')->delete();
        }

    }

}
