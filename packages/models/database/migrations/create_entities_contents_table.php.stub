<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEntitiesContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities_contents', function (Blueprint $table) {
            $table->id('content_id');
            $table->string('entity_id', 32)->index();
            $table->char('lang', 5)->index();
            $table->string('field', 32)->index();
            $table->text('text')->nullable();
            $table->timestampsTz();
            $table->unique(['entity_id', 'lang', 'field']);
            $table->foreign('entity_id')
                ->references('id')->on('entities')
                ->onDelete('cascade')->onUpdate('cascade');
        });
        switch(env('DB_CONNECTION')) {
            case 'mysql':
                DB::statement('create fulltext index entities_contents_value_fulltext on entities_contents(text);');
                break;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities_contents', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('entities_contents');
            if ($doctrineTable->hasIndex('entities_contents_value_fulltext')) {
                $table->dropIndex('entities_contents_value_fulltext');
            }
        });
        Schema::dropIfExists('entities_contents');
    }
}
