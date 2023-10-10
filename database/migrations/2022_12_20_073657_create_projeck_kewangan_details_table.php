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
        Schema::create('Projek_Kewangan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_projek_id')->constrained('projects');

            $table->decimal('impak_keseluruhan', $precision = 38, $scale = 2)->nullable();
            $table->decimal('ci', $precision = 38, $scale = 2)->nullable();

            $table->decimal('totalkos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('totalkos_perunding', $precision = 20, $scale = 2)->nullable();
            $table->decimal('Siling_Dimohon', $precision = 20, $scale = 2)->nullable();
            $table->decimal('Siling_Bayangan', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kos_keseluruhan', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kos_keseluruhan_oe', $precision = 20, $scale = 2)->nullable();
            $table->decimal('imbuhan_balik', $precision = 20, $scale = 2)->nullable();
            $table->decimal('temp_sst_tax', $precision = 20, $scale = 2)->nullable();
            $table->decimal('jumlahkos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('temp_jumlahkos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('sst_tax', $precision = 20, $scale = 2)->nullable();
            $table->decimal('anggaran_mainworks', $precision = 20, $scale = 2)->nullable();
            $table->decimal('P_max', $precision = 20, $scale = 2)->nullable();
            $table->decimal('P_min', $precision = 20, $scale = 2)->nullable();
            $table->decimal('P_avg', $precision = 20, $scale = 2)->nullable();
            $table->integer('P_selection')->nullable();
            $table->decimal('design_fee', $precision = 20, $scale = 2)->nullable();
            $table->decimal('imbuhanbalik_piawai', $precision = 20, $scale = 2)->nullable();
            $table->decimal('cukai_sst', $precision = 20, $scale = 2)->nullable();
            $table->decimal('anggarankos_piawai', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_perunding_kos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_professional', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_subprofessional', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_imbuhanbalik', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_ssttax', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_anggaran', $precision = 20, $scale = 2)->nullable();   
            
            $table->decimal('yuran_perunding_kos_tapak', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_professional_tapak', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_subprofessional_tapak', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_imbuhanbalik_tapak', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_ssttax_tapak', $precision = 20, $scale = 2)->nullable();
            $table->decimal('yuran_anggaran_tapak', $precision = 20, $scale = 2)->nullable();

            $table->integer('Komponen_id');
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
        Schema::dropIfExists('Projek_Kewangan_details');
    }
};
