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
        if (Schema::hasTable('groups')){
            Schema::rename('groups', 'roles');
            DB::statement('ALTER TABLE "roles" ALTER COLUMN "created_at" SET DEFAULT now();');
            DB::statement('ALTER TABLE "roles" ALTER COLUMN "updated_at" SET DEFAULT now();');
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
            Schema::rename('roles', 'groups');
            DB::statement('ALTER TABLE "groups" ALTER COLUMN "created_at" DROP DEFAULT;');
            DB::statement('ALTER TABLE "groups" ALTER COLUMN "updated_at" DROP DEFAULT;');
        }
    }

}
