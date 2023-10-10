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
        Schema::create('peringkat_rmk', function (Blueprint $table) {
            $table->string('kod_projek',50)->nullable();
            $table->string('tonggak',200);
            $table->string('bidang_keutamaan',500)->nullable();
            $table->string('outcome_nasional',500)->nullable();
            $table->string('strategi',500);
            $table->string('sasaran_pencapaian',200);
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
        Schema::dropIfExists('peringkat_rmk');
    }
};
