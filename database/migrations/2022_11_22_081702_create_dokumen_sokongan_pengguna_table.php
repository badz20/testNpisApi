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
        Schema::create('dokumen_sokongan_pengguna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pengguna');
            $table->foreign('id_pengguna')->references('id')->on('users');
            $table->string('nama_dokumen',250);
            $table->string('dokumen',500);            
            $table->integer('dibuat_oleh');
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
        Schema::dropIfExists('dokumen_sokongan_pengguna');
    }
};
