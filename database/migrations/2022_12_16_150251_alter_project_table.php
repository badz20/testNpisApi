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
        Schema::table('projects', function (Blueprint $table) {
            
            $table->integer('penyemak_1')->nullable();
            $table->string('penyemak_1_catatan',1000)->nullable();
            $table->dateTime('penyemak_1_review_date')->nullable();
            $table->integer('penyemak_2')->nullable();
            $table->string('penyemak_2_catatan',1000)->nullable();
            $table->dateTime('penyemak_2_review_date')->nullable();
            $table->integer('pengesah')->nullable();
            $table->string('pengesah_catatan',1000)->nullable();
            $table->dateTime('pengesah_review_date')->nullable();

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
            //
        });
    }
};
