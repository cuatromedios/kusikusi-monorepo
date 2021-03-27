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
            $table->string('id', 32)->primary();
            $table->string('model', 32)->index();
            $table->json('properties')->nullable();
            $table->string('view', 32)->nullable();
            $table->json('langs')->nullable();
            $table->string('parent_entity_id', 32)->index('parent')->nullable();
            $table->string('visibility', 32)->nullable('public')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('published_at')->default('2000-01-01 00:00:00');
            $table->dateTime('unpublished_at')->nullable();
            $table->integer('version')->unsigned()->default(1);
            $table->integer('version_tree')->unsigned()->default(1);
            $table->integer('version_relations')->unsigned()->default(1);
            $table->integer('version_full')->unsigned()->default(1);
            $table->timestampsTz();
            $table->softDeletesTz();
            // Uncomment if you want a foreign key to users table for created_by and / or updated_by
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            // $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
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
