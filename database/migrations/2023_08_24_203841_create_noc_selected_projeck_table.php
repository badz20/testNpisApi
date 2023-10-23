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
        Schema::create('noc_selected_projeck', function (Blueprint $table) {
            $table->id();
            $table->integer('noc_id');
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->string('no_rujukan',50)->nullable();
            $table->string('kod_projeck',50)->nullable();
            $table->string('butiran_code',10)->nullable();
            $table->string('nama_projek',1000)->nullable();
            $table->string('pembiyan',1000)->nullable();
            $table->decimal('kos_projeck', $precision = 20, $scale = 2)->nullable();
            $table->decimal('keseruluhan_kos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('baki_kos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('peruntukan_kos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('peruntukan_asal ', $precision = 20, $scale = 2)->nullable();
            $table->decimal('tambah ', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kurang ', $precision = 20, $scale = 2)->nullable();
            $table->decimal('dipinda ', $precision = 20, $scale = 2)->nullable();
            $table->text('justifikasi')->nullable();
            $table->boolean('type')->default(0);
            $table->foreignId('bahagian_pemilik')->constrained('ref_bahagian');
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->integer('status_id')->nullable();
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
        Schema::dropIfExists('noc_selected_projeck');
    }
};
