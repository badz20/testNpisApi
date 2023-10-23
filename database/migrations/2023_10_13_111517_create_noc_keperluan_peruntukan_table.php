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
        Schema::create('noc_keperluan_peruntukan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->date('tarikh_kemaskini')->nullable();
            $table->decimal('kepeluruan_amaun',$precision = 38, $scale = 2)->default(0);
            $table->text('justifikasi')->nullable();
            $table->date('rekod_permohonan')->nullable();
            $table->decimal('amaun',$precision = 38, $scale = 2)->default(0);
            $table->date('taikh_waran')->nullable();
            $table->decimal('waran_tambahan',$precision = 38, $scale = 2)->default(0);
            $table->decimal('waran_pemulangan',$precision = 38, $scale = 2)->default(0);
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
        Schema::dropIfExists('noc_keperluan_peruntukan');
    }
};
