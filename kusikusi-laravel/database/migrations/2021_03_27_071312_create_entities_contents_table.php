<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities_contents');
    }
}
