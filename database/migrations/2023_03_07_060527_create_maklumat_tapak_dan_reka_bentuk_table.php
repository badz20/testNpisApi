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
        Schema::create('maklumat_tapak_dan_reka_bentuk', function (Blueprint $table) {
            $table->string('kod_projek',50)->nullable();
            $table->string('lokasi_spesifikasi_teknikal',200);
            $table->string('keluasan_tapak',200);
            $table->string('syarat_kegunaan_tanah',400);
            $table->string('cadangan_berkaitan_utiliti',200);
            $table->string('cadangan_kegunaan_tanah_dibincang',200);
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
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
        Schema::dropIfExists('maklumat_tapak_dan_reka_bentuk');
    }
};
