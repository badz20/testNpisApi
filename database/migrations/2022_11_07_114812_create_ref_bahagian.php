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
        Schema::create('ref_bahagian', function (Blueprint $table) {
            $table->id();
            $table->string('kod_bahagian', 50);
            $table->string('acym', 20);
            $table->string('nama_bahagian', 100);
            $table->string('penerangan_bahagian',500)->nullable();
            $table->foreignId('kementerian_id')->constrained('ref_kementerian');
            $table->foreignId('jabatan_id')->constrained('ref_jabatan');
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->boolean('row_status')->nullable();
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
        Schema::dropIfExists('ref_bahagian');
    }
};
