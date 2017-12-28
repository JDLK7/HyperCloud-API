<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('name');
            $table->integer('size');
            $table->string('extension')->nullable();
            $table->string('path');
            $table->string('type');

            $table->primary('id');

            $table->char('account_id', 36)->nullable();
            $table->char('group_id', 36)->nullable();
            $table->foreign('account_id')->references('id')
                ->on('accounts')->onDelete('cascade');
            $table->foreign('group_id')->references('id')
                ->on('groups')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
