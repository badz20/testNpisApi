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
        Schema::create('project_kajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('jenis_kajian_code',10);
            $table->string('kategori_hakisan',10)->nullable();
            $table->integer('tahun_siap_terkini')->nullable();
            $table->string('nama_laporan',200)->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->useCurrent();            
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
        Schema::dropIfExists('project_kajian');
    }
};
