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
        Schema::rename('groups', 'roles');
        DB::raw('ALTER TABLE "roles" ALTER COLUMN "created_at" SET DEFAULT now();');
        DB::raw('ALTER TABLE "roles" ALTER COLUMN "updated_at" SET DEFAULT now();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('roles', 'groups');
        DB::raw('ALTER TABLE "groups" ALTER COLUMN "created_at" DROP DEFAULT;');
        DB::raw('ALTER TABLE "groups" ALTER COLUMN "updated_at" DROP DEFAULT;');
    }

}
