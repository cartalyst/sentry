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
        if (Schema::hasTable('roles')){
            Schema::table('roles', function($table)
            {
                $table->dropColumn('permissions');
            });

            Schema::table('roles', function($table)
            {
                $table->string('code');
            });

            DB::table('roles')->delete();

            DB::table('roles')->insert(
                array(
                    'code' => 'admin',
                    'name' => 'Administrator'
                )
            );

            DB::table('roles')->insert(
                array(
                    'code' => 'user',
                    'name' => 'Użytkownik'
                )
            );

            DB::table('roles')->insert(
                array(
                    'code' => 'guest',
                    'name' => 'Gość'
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
            Schema::table('roles', function($table)
            {
                $table->text('permissions')->nullable();
            });

            Schema::table('roles', function($table)
            {
                $table->dropColumn('code');
            });
        }

    }

}
