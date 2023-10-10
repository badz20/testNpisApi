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
        Schema::create('perunding_maklumats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan_id');
            $table->foreign('perolehan_id')->references('id')->on('pemantauan_perolehans');
            $table->boolean('bayaran_perunding')->default(0);
            $table->integer('email_peringatan')->nullable();
            $table->decimal('kos_perolehan', $precision = 20, $scale = 2)->default(0)->nullable();
            $table->decimal('nilai_bayaran_akhir_selesai', $precision = 20, $scale = 2)->default(0);
            $table->decimal('penjimatan_selesai', $precision = 20, $scale = 2)->default(0);
            $table->decimal('nilai_bayaran_akhir_tamat', $precision = 20, $scale = 2)->default(0);
            $table->decimal('penjimatan_tamat', $precision = 20, $scale = 2)->default(0);
            $table->string('no_polisi')->nullable();
            $table->decimal('nilai_polisi', $precision = 20, $scale = 2)->default(0);
            $table->date('perlindungan_tarikh_mula')->nullable();
            $table->date('perlindungan_tarikh_tamat')->nullable();
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
        Schema::dropIfExists('perunding_maklumats');
    }
};