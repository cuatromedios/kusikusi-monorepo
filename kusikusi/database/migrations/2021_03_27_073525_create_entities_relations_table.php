<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities_relations', function (Blueprint $table) {
            $table->id('relation_id');
            $table->string('caller_entity_id', 32)->index('caller');
            $table->string('called_entity_id', 32)->index('called');
            $table->string('kind', 25)->default('relation')->index('kind');
            $table->integer('position')->unsigned()->default(0);
            $table->integer('depth')->unsigned()->default(0);
            $table->json('tags')->nullable();
            $table->timestampsTz();
            $table->foreign('caller_entity_id')
                ->references('id')->on('entities')
                ->onDelete('cascade')->onUpdate('cascade');;
            $table->foreign('called_entity_id')
                ->references('id')->on('entities')
                ->onDelete('restrict')->onUpdate('cascade');;
            $table->unique(['caller_entity_id', 'called_entity_id', 'kind'], 'relation_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_relations');
    }
}
