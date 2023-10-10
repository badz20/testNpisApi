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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('no_rujukan',50)->nullable();
            $table->string('kod_projeck',50)->nullable();
            $table->unsignedBigInteger('negeri_id')->nullable();
            $table->foreign('negeri_id')->references('id')->on('ref_negeri');
            $table->unsignedBigInteger('daerah_id')->nullable();
            $table->foreign('daerah_id')->references('id')->on('ref_daerah');
            $table->string('kategori_Projek',200);
            $table->foreignId('bahagian_pemilik')->constrained('ref_bahagian');
            $table->string('rolling_plan_code',10);
            $table->string('rmk',10);
            $table->string('butiran_code',10);
            $table->string('nama_projek',1000);
            $table->text('objektif');
            $table->text('ringkasan_projek');
            $table->text('rasional_projek');
            $table->text('Faedah');
            $table->integer('tahun');
            $table->integer('kos_projeck')->nullable();
            $table->string('negeri')->nullable();
            $table->string('jenis_kategori_code',10);
            $table->string('jenis_sub_kategori_code',10);
            $table->text('implikasi_projek_tidak_lulus');
            $table->foreignId('bahagian_epu_id')->constrained('ref_bahagian_epu_jpm');
            $table->foreignId('sektor_utama_id')->constrained('ref_sektor_utama');
            $table->foreignId('sektor_id')->constrained('ref_sektor');
            $table->foreignId('sub_sektor_id')->constrained('ref_sub_sektor');
            $table->string('koridor_pembangunan',10);
            $table->char('kululusan_khas',1);
            $table->text('nota_tambahan')->nullable();
            $table->char('sokongan_upen',1);
            $table->integer('tahun_jangka_mula');
            $table->integer('tahun_jangka_siap');
            $table->integer('tempoh_pelaksanaan');
            $table->char('kajian',1);
            $table->string('jenis_kajian',10)->nullable();
            $table->integer('tahun_kajian_siap_terkini')->nullable();
            $table->string('kategori_hakisan',10)->nullable();
            $table->string('nama_laporan_kajian',200)->nullable();
            $table->char('rujukan_pelan_induk',1);
            $table->string('rujukan_code',10)->nullable();
            $table->string('nama_laporan_pelan_induk',200)->nullable();
            $table->integer('rujukan_tahun_siap')->nullable();
            $table->char('status_reka_bantuk',2)->nullable();
            $table->string('reka_bantuk_siap',10)->nullable();
            $table->char('melibat_pembinaan_fasa',1);
            $table->string('melibat_pembinaan_fasa_description',200)->nullable();
            $table->integer('melibat_pembinaan_fasa_status')->nullable();
            $table->integer('melibat_pembinaan_fasa_tahun')->nullable();
            $table->string('kekerapan_banjir_code',10);
            $table->string('workflow_status',20);
            $table->char('pernah_dibahasakan',1);
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();            
            $table->boolean('row_status')->default(1);
            $table->boolean('negeri_selection_type')->default(0);
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
        Schema::dropIfExists('project');
    }
};
