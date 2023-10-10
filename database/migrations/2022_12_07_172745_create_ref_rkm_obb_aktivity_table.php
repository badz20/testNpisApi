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
        Schema::create('ref_rkm_obb_aktivity', function (Blueprint $table) {
            $table->id();
            $table->string('kod_aktivity',50);
            $table->string('nama_aktivity',500);
            $table->string('obb_program',500)->nullable();
            $table->string('obb_aktiviti',500)->nullable();
            $table->string('catatan',500)->nullable();
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
        Schema::dropIfExists('ref_rkm_obb_aktivity');
    }
};
