<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->string('route_id', 16)->primary();
            $table->string('entity_id', 16)->index()->nullable();
            $table->string('path')->index()->nullable();
            $table->string('entity_model', 16);
            $table->char('lang', 5)->index()->nullable();
            $table->boolean('default')->default(false);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->foreign('entity_id')->references('id')->on('entities')
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
        Schema::dropIfExists('routes');
    }
}
