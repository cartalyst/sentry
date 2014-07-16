<?php
use Illuminate\Database\Migrations\Migration;

class CreateDefaultUsersRoles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users_roles')->where('name', 'admin')->delete();
        DB::table('users_roles')->where('name', 'user')->delete();
        DB::table('users_roles')->where('name', 'quest')->delete();
    }

}
