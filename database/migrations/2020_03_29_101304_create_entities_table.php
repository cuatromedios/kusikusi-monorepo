<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->string('model', 32);
            $table->json('properties')->nullable();
            $table->string('view', 32)->nullable();
            $table->string('parent_entity_id', 16)->index('parent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->dateTime('published_at')->default('2000-01-01 00:00:00');
            $table->dateTime('unpublished_at')->nullable();
            $table->integer('version')->unsigned()->default(0);
            $table->integer('version_tree')->unsigned()->default(0);
            $table->integer('version_relations')->unsigned()->default(0);
            $table->integer('version_full')->unsigned()->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
