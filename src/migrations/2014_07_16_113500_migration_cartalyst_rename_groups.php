<?php
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystRenameGroups extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('roles')){
            Schema::table('roles', function($table) {
                $table->dropColumn('permissions');
                $table->dropColumn('permissions');
                $table->dropColumn('permissions');
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
        if (Schema::hasTable('roles')){
            Schema::table('roles', function($table) {
                $table->text('permissions')->nullable();
            });
        }

    }

}
