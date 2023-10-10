<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         //
         Schema::table('projects', function (Blueprint $table) {
            $table->integer('kod_asal')->nullable();
            $table->integer('kod_baharu')->nullable();
            $table->integer('peraku')->nullable();
            $table->dateTime('peraku_review_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('kod_asal');
            $table->dropColumn('kod_baharu');
            $table->dropColumn('peraku');
            $table->dropColumn('peraku_review_date');
        });
    }
};
