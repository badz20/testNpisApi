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
        Schema::create('perunding_penilaians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan_id');
            $table->foreign('perolehan_id')->references('id')->on('pemantauan_perolehans');

            $table->string('deliverable')->nullable();

            $table->integer('jadual_pelaksanaan')->nullable();
            $table->integer('skop_perkhidmatan')->nullable();
            $table->integer('pengurusan_sumber')->nullable();
            $table->integer('keupayaan_teknikal')->nullable();
            $table->integer('kualiti_kerja')->nullable();
            $table->integer('kerjasama')->nullable();
            $table->integer('peruntukan_diluluskan')->nullable();
            $table->integer('pengawasan')->nullable();

            $table->integer('lemah_jumlah')->nullable();
            $table->integer('sederhana_jumlah')->nullable();
            $table->integer('baik_jumlah')->nullable();
            $table->integer('sangat_baik_jumlah')->nullable();
            $table->integer('total_jumlah')->nullable();
            $table->string('penilaian_keseluruhan')->nullable();
            $table->boolean('is_disyorkan')->default(0);
            $table->string('catatan')->nullable();

            $table->date('tarikh_penilaian')->nullable();
            
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
        Schema::dropIfExists('perunding_penilaians');
    }
};