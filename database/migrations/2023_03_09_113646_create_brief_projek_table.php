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
        Schema::create('brief_projek', function (Blueprint $table) {
            $table->string('jenis_permohonan_projek',200);
            $table->string('kategori_projek',200);
            $table->string('rancangan_malaysia_ke',200);
            $table->string('rolling_plan',10);
            $table->string('kod_projek',50)->nullable();
            $table->string('nama_projek',400);
            $table->integer('status_perlaksanaan')->nullable();
            $table->string('jenis_kategori_projek',10);
            $table->integer('bahagian')->nullable();
            $table->integer('sektor_utama')->nullable();
            $table->string('sektor',50);
            $table->string('skop', 50);
            $table->text('objektif');
            $table->text('keterangan_projek_komponen');
            $table->integer('negeri')->nullable();
            $table->integer('daerah')->nullable();
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
        Schema::dropIfExists('brief_projek');
    }
};
