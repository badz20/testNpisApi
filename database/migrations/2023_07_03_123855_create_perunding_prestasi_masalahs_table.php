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
        Schema::create('perunding_prestasi_masalahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan_id');
            $table->foreign('perolehan_id')->references('id')->on('pemantauan_perolehans');
            $table->unsignedBigInteger('prestasi_id');
            $table->foreign('prestasi_id')->references('id')->on('perunding_prestasis')->onDelete('cascade');
            $table->string('masalah')->nullable();
            $table->date('tarikh_masalah')->nullable();
            $table->boolean('is_done')->default(0);
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
        Schema::dropIfExists('perunding_prestasi_masalahs');
    }
};