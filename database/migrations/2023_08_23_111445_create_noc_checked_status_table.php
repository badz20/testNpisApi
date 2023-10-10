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
        Schema::create('noc_checked_status', function (Blueprint $table) {
            $table->id();
            $table->integer('noc_id')->nullable();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->boolean('skop_status')->default(0);
            $table->boolean('kos_status')->default(0);
            $table->boolean('butiran_status')->default(0);
            $table->boolean('semula_status')->default(0);
            $table->boolean('nama_status')->default(0);
            $table->boolean('lokasi_status')->default(0);
            $table->boolean('kpi_status')->default(0);
            $table->boolean('outcome_status')->default(0);
            $table->boolean('kod_status')->default(0);
            $table->boolean('objectif_status')->default(0);
            $table->boolean('output_status')->default(0);            
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->integer('status_id')->nullable();
            $table->boolean('row_status')->default(1);
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
        Schema::dropIfExists('noc_checked_status');
    }
};
