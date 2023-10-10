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
        Schema::create('ref_strategi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_strategi',500);
            $table->string('Tema_Pemangkin_Dasar',500)->nullable();
            $table->string('Bab',500)->nullable();
            $table->string('Bidang_Keutamaan',500)->nullable();
            $table->string('Outcome_Nasional',500)->nullable();
            $table->string('Catatan',500)->nullable();
            $table->string('kod_strategi',50)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent()->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('is_hidden')->default(1);
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
        Schema::dropIfExists('ref_strategi');
    }
};
