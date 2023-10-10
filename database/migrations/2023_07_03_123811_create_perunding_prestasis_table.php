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
        Schema::create('perunding_prestasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan_id');
            $table->foreign('perolehan_id')->references('id')->on('pemantauan_perolehans');
            $table->integer('tahun')->nullable();
            $table->integer('bulan')->nullable();
            $table->string('deliverable')->nullable();
            $table->string('emel')->nullable();
            $table->date('tarikh_mula_jadual')->nullable();
            $table->date('tarikh_mula_sebenar')->nullable();
            $table->date('tarikh_siap_jadual')->nullable();
            $table->date('tarikh_siap_sebenar')->nullable();
            $table->integer('hari_lewat_awal')->nullable();
            $table->string('peratus_jadual')->nullable();
            $table->string('peratus_sebenar')->nullable();
            $table->string('status_pelaksanaan')->nullable();
            $table->date('tarikh_mesyuarat')->nullable();
            $table->string('keputusan')->nullable();
            $table->string('penilaian')->nullable();
            $table->date('EOT')->nullable();
            $table->date('tarikh_lad_mula')->nullable();
            $table->date('tarikh_lad_tamat')->nullable();
            $table->integer('bilangan_hari_lad')->nullable();
            $table->decimal('jumlah_lad_terkumpul', $precision = 20, $scale = 2)->default(0)->nullable();
            $table->date('tarikh_kemaskini')->nullable();
            $table->integer('version_no')->nullable();
            $table->integer('is_readonly')->boolean()->default(0);
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
        Schema::dropIfExists('perunding_prestasis');
    }
};