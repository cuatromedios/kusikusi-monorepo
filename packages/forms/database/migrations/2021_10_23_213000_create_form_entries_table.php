<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('entry')->nullable();
            $table->string('entity_id', 32)->index();
            $table->string('status', 32)->nullable('unread')->index();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->foreign('entity_id')
                ->references('id')->on('entities')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_entries');
    }
}
