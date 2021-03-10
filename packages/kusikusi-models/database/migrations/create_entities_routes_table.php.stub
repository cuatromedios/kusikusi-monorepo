<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities_routes', function (Blueprint $table) {
            $table->id('route_id');
            $table->string('entity_id', 32)->index()->nullable();
            $table->string('path')->index()->nullable();
            $table->string('entity_model', 32);
            $table->char('lang', 5)->index()->nullable();
            $table->string('kind', 32)->index();
            $table->timestampsTz();
            $table->foreign('entity_id')
                ->references('id')->on('entities')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_routes');
    }
}
