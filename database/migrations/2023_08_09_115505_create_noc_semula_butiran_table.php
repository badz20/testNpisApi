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
        Schema::create('noc_semula_butiran', function (Blueprint $table) {
            $table->id();
            $table->integer('pp_id')->nullable();
            $table->integer('noc_id');
            $table->string('nama_projek', 255)->nullable();
            $table->string('kod_projek');
            $table->string('justifikasi', 255)->nullable();
            $table->decimal('kos_projek', $precision = 20, $scale = 2)->nullable();
            $table->decimal('keperluan', $precision = 20, $scale = 2)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent()->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
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
        Schema::dropIfExists('noc_semula_butiran');
    }
};
