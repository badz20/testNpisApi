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
        Schema::create('RMK_OBB_SDG', function (Blueprint $table) {
            $table->id();
            $table->string('kod_sdg',50)->nullable();
            $table->string('Pemangkin_Dasar',500);
            $table->foreignId('permohonan_projek_id')->constrained('projects');
            $table->string('Bab',500);
            $table->string('Bidang_Keutamaan',500);
            $table->string('Outcome_Nasional',500);
            $table->string('Strategi',500);
            $table->string('OBB_Program',500);
            $table->string('OBB_Aktiviti',500);

            $table->integer('OBB_Output_Aktiviti_id');
            $table->integer('SDG_id')->nullable();
            $table->integer('Indikatori_id')->nullable();
            $table->integer('Sasaran_id')->nullable();

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
        Schema::dropIfExists('RMK_OBB_SDG');
    }
};
