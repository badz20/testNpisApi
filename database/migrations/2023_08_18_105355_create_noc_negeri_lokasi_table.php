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
        Schema::create('noc_negeri_lokasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->integer('noc_id')->nullable();
            $table->string('no_rujukan',50)->nullable();
            $table->integer('negeri_id');
            $table->integer('daerah_id');
            $table->integer('mukim_id')->nullable();
            $table->integer('parlimen_id');
            $table->integer('dun_id');
            $table->integer('permohonan_Projek_id')->nullable();
            $table->string('koordinat_latitude',20)->nullable();
            $table->string('koordinat_longitude',20)->nullable();
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
        Schema::dropIfExists('noc_negeri_lokasi');
    }
};
