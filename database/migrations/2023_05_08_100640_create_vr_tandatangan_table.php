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
        Schema::create('vr_tandatangan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->string('jenis_jabatan',50); 
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->integer('kategori_tandatangan');
            $table->date('tarikh_tandatangan');
            $table->string('tandatangan_file_name', 200);
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
        Schema::dropIfExists('vr_tandatangan');
    }
};
