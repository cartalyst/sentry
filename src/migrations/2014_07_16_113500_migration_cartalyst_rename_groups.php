<?php
use Illuminate\Database\Migrations\Migration;

class RenameGroupsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('groups', 'roles');
        DB::query('ALTER TABLE "roles" ALTER COLUMN "created_at" SET DEFAULT now();');
        DB::query('ALTER TABLE "roles" ALTER COLUMN "updated_at" SET DEFAULT now();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('roles', 'groups');
        DB::query('ALTER TABLE "groups" ALTER COLUMN "created_at" DROP DEFAULT;');
        DB::query('ALTER TABLE "groups" ALTER COLUMN "updated_at" DROP DEFAULT;');
    }

}
