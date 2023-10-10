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
        Schema::create('pemantauan_perolehans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->string('nama_perolehan', 255);
            $table->string('nama_peruding', 255);
            $table->string('jenis_perkhidmatan', 255);
            $table->string('no_documen_perolehan', 255);
            $table->string('kaedah_perolehan', 255);
            $table->string('no_pendaftaran_moh', 255);
            $table->date('tarikh_setuju_terima');
            $table->date('tarikh_mula_perkhidmatan');
            $table->date('tarikh_tamat_perjanjian');
            $table->decimal('kos_perolehan',$precision = 20, $scale = 2)->nullable();
            $table->string('yuran_perunding', 255);
            $table->string('eocp', 255);
            $table->integer('kemajuan_jadual');
            $table->integer('kemajuan_sebenar');
            $table->string('penilaiyan', 255);
            $table->string('status_pelaksanaan', 255);
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
        Schema::dropIfExists('pemantuan_perolehan');
    }
};