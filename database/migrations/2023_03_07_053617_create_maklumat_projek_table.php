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
        Schema::create('maklumat_projek', function (Blueprint $table) {
            $table->string('nama_projek',400);
            $table->string('kod_projek',50)->nullable();
            $table->string('kategori_projek',200);
            $table->string('jenis_kategori_projek',10);
            $table->decimal('kos_keseluruhan', $precision = 20, $scale = 2)->nullable();
            $table->decimal('status_perlaksanaan', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kemajuan_semasa', $precision = 20, $scale = 2)->nullable();
            $table->string('rolling_plan',10);
            $table->string('maksud_pembangunan_kementerian',200);
            $table->decimal('butiran_program', $precision = 20, $scale = 2)->nullable();
            $table->foreignId('sektor_utama')->constrained('ref_sektor_utama');
            $table->foreignId('sub_sektor')->constrained('ref_sub_sektor');
            $table->string('kawasan',200);
            $table->string('indikator_projek',200);
            $table->integer('tahun_jangka_mula');
            $table->integer('tempoh_perlaksanan');
            $table->string('sub_kategori',50);
            $table->string('sektor',50);
            $table->string('koridor_pembangunan',10);
            $table->integer('tahun_jangka_siap');
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
        Schema::dropIfExists('maklumat_projek');
    }
};
