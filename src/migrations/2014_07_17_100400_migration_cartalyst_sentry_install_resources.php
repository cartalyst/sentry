<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystSentryInstallResources extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('resources')){
            Schema::create('resources', function($table)
            {
                $table->increments('id');
                $table->integer('parent_id')->unsigned()->nullable();
                $table->string('name');
                $table->string('value');


                // We'll need to ensure that MySQL uses the InnoDB engine to
                // support the indexes, other engines aren't affected.
                $table->engine = 'InnoDB';
                $table->index('value');
                $table->index('name');
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
        if (Schema::hasTable('resources')){
            Schema::drop('resources');
        }

    }

}
