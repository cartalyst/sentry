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
        if (Schema::hasTable('roles')){
            DB::table('roles')->insert(
                array(
                    'name' => 'admin'
                )
            );

            DB::table('roles')->insert(
                array(
                    'name' => 'user'
                )
            );

            DB::table('roles')->insert(
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
        if (Schema::hasTable('roles')){
            DB::table('roles')->where('name', 'admin')->delete();
            DB::table('roles')->where('name', 'user')->delete();
            DB::table('roles')->where('name', 'quest')->delete();
        }

    }

}
