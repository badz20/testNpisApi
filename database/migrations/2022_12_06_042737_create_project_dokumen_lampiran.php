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
        Schema::create('project_dokumen_lampiran', function (Blueprint $table) {
            $table->id();
            $table->integer('permohonan_projek_id');
            $table->string('lfm_dokumen_nama',100)->nullable();
            $table->string('lfm_dokumen',100)->nullable();
            $table->string('perakuan_pengesahan_dokumen_nama',100)->nullable();
            $table->string('perakuan_pengesahan_dokumen',100)->nullable();
            $table->string('lain_lain_dokumen_nama1',100)->nullable();
            $table->string('lain_lain_dokumen1',100)->nullable();
            $table->string('lain_katerangan_documen1',500)->nullable();
            $table->string('lain_lain_dokumen_nama2',100)->nullable();
            $table->string('lain_lain_dokumen2',100)->nullable();
            $table->string('lain_katerangan_documen2',500)->nullable();
            $table->string('lain_lain_dokumen_nama3',100)->nullable();
            $table->string('lain_lain_dokumen3',100)->nullable();
            $table->string('lain_katerangan_documen3',500)->nullable();
            $table->string('lain_lain_dokumen_nama4',100)->nullable();
            $table->string('lain_lain_dokumen4',100)->nullable();
            $table->string('lain_katerangan_documen4',500)->nullable();
            $table->string('lain_lain_dokumen_nama5',100)->nullable();
            $table->string('lain_lain_dokumen5',100)->nullable();
            $table->string('lain_katerangan_documen5',500)->nullable();
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
        Schema::dropIfExists('project_dokumen_lampiran');
    }
};
