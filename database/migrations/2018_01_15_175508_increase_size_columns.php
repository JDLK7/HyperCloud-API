<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseSizeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->bigInteger('space')->change();
        });
        Schema::table('suscriptions', function (Blueprint $table) {
            $table->bigInteger('spaceOffer')->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('space');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('space')->unsigned()->nullable();
        });
        Schema::table('suscriptions', function (Blueprint $table) {
            $table->dropColumn('spaceOffer');
        });
        Schema::table('suscriptions', function (Blueprint $table) {
            $table->integer('spaceOffer');
        });
    }
}
