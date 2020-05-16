<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->string('content_id', 16);
            $table->string('entity_id', 16)->index();
            $table->char('lang', 5)->index();
            $table->string('field', 25)->index();
            $table->text('text');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index('entity_id', 'lang');
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
        Schema::dropIfExists('contents');
    }
}
