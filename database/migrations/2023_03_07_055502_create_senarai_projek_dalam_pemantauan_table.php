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
        Schema::create('senarai_projek_dalam_pemantauan', function (Blueprint $table) {

            $table->string('rancangan_malaysia_ke',200);
            $table->string('rolling_plan',10);
            $table->string('aktiviti_semasa',200);
            $table->string('nama_projek',400);
            $table->string('kod_projek',50)->nullable();
            $table->string('kategori_projek',200);
            $table->string('jenis_kategori_projek',10);
            $table->decimal('kos_keseluruhan', $precision = 20, $scale = 2)->nullable();
            $table->decimal('status_perlaksanaan', $precision = 20, $scale = 2)->nullable();
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
        Schema::dropIfExists('senarai_projek_dalam_pemantauan');
    }
};
