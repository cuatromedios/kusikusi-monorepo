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
            $table->id('archive_id');
            $table->string('entity_id', 32)->nullable()->index();
            $table->string('kind', 32)->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('payload');
            $table->timestampsTz();
            $table->foreign('entity_id')
                ->references('id')->on('entities')
                ->onDelete('set null')
                ->onUpdate('cascade');
            // Uncomment if you want a foreign key to users table for created_by and / or updated_by
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
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
