<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relations', function (Blueprint $table) {
            $table->string('relation_id', 16)->primary();
            $table->string('caller_entity_id', 16)->index('caller');
            $table->string('called_entity_id', 16)->index('called');
            $table->string('kind', 25)->default('relation')->index('kind');
            $table->integer('position')->unsigned()->default(0);
            $table->integer('depth')->unsigned()->default(0);
            $table->json('tags')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->foreign('caller_entity_id')->references('id')->on('entities')
                ->onDelete('cascade')->onUpdate('cascade');;
            $table->foreign('called_entity_id')->references('id')->on('entities')
                ->onDelete('restrict')->onUpdate('cascade');;
            $table->index(['caller_entity_id', 'called_entity_id', 'kind'], 'relation_search');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relations');
    }
}
