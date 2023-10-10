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
        Schema::create('_projek_vae', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Permohonan_Projek_id')->constrained('projects');
            //$table->integer('Permohonan_Projek_id');
            $table->integer('Acquisition_Cost');
            $table->integer('Acquisition_Cost_score');
            $table->integer('Project_Management');
            $table->integer('Project_Management_score'); 
            $table->integer('Schedule'); 
            $table->integer('Schedule_scope'); 
            $table->integer('Technical_Difficulty'); 
            $table->integer('Technical_Difficulty_score'); 
            $table->integer('Operation_Maintainance'); 
            $table->integer('Operation_Maintainance_score');
            $table->integer('Industry');
            $table->integer('Industry_score'); 
            $table->integer('ACAT_score'); 
            $table->string('ACAT',50);            
            $table->boolean('proj_viability_1_1_a')->default(1);
            $table->boolean('proj_viability_1_2_a')->default(1);
            $table->boolean('brif_2_1_a')->default(1);
            $table->boolean('brif_2_2_a')->default(1);
            $table->boolean('brif_2_2_b')->default(1);
            $table->boolean('brif_2_2_c')->default(1);
            $table->boolean('brif_2_2_d')->default(1);
            $table->boolean('brif_2_3_a')->default(1);
            $table->boolean('brif_2_3_b')->default(1);
            $table->boolean('brif_2_3_c')->default(1);
            $table->boolean('brif_2_4_a')->default(1);
            $table->boolean('brif_2_4_b')->default(1);
            $table->boolean('tanah_3_1_a')->default(1);
            $table->boolean('tanah_3_1_b')->default(1);
            $table->boolean('tanah_3_2_a')->default(1);
            $table->boolean('tanah_3_2_b')->default(1);
            $table->boolean('anggaran_4_1_a')->default(1);
            $table->boolean('anggaran_4_2_a')->default(1);
            $table->boolean('anggaran_4_3_a')->default(1);
            $table->boolean('anggaran_4_4_a')->default(1);
            $table->boolean('anggaran_4_5_a')->default(1);
            $table->boolean('anggaran_4_6_a')->default(1);
            $table->boolean('anggaran_4_7_a')->default(1);
            $table->boolean('anggaran_4_8_a')->default(1);
            $table->boolean('Pelaksanaan_5_1_a')->default(1);
            $table->boolean('Pelaksanaan_5_2_a')->default(1);
            $table->string('GNO_status')->nullable();
            $table->string('Pegawa_Penyedia',100)->nullable();
            $table->string('Pegawa_Penyedia_Jawatan',100)->nullable();
            $table->string('Pegawa_Penyedia_Ulasan',100)->nullable();
            $table->string('Pegawa_Penyedia_Tandatangan',100)->nullable();
            $table->string('Pegawai_Deberi_Kuasa',100)->nullable();
            $table->string('KSU_Jawatan',100)->nullable();
            $table->string('KSU_Tarikh_Keputusan',100)->nullable();
            $table->string('KSU_Ulusan',100)->nullable();
            $table->string('KSU_Tandatangan',100)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
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
        Schema::dropIfExists('_projek_vae');
    }
};
