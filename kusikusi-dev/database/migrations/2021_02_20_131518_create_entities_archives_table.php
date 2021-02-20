<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities_archives', function (Blueprint $table) {
            $table->id('version_id');
            $table->string('entity_id', 32)->index();
            $table->string('kind', 32)->index();
            $table->unsignedInteger('version');
            $table->json('payload');
            $table->timestampsTz();
            $table->foreign('entity_id')
                ->references('id')->on('entities')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_archives');
    }
}
