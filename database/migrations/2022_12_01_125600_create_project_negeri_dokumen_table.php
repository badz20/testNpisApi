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
        Schema::create('project_negeri_dokumen', function (Blueprint $table) {
            $table->id();
            $table->integer('permohonan_Projek_id'); 
            $table->string('projek_negeri_dokumen_name',100);
            $table->string('keterangan',500);
            $table->integer('dibuat_oleh');
            $table->dateTime('dibuat_pada');
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
        Schema::dropIfExists('project_negeri_dokumen');
    }
};
