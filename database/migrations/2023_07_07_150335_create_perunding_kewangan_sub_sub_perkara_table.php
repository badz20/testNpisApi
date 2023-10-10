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
        Schema::create('perunding_kewangan_sub_sub_perkara', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perkara_id');
            $table->foreign('perkara_id')->references('id')->on('perunding_kewangan_perkara');
            $table->unsignedBigInteger('sub_perkara_id');
            $table->foreign('sub_perkara_id')->nullable()->references('id')->on('perunding_kewangan_sub_perkara');
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan');
            $table->foreign('perolehan')->references('id')->on('pemantauan_perolehans');
            $table->integer('no_bayaran');
            $table->string('sub_sub_perkara', 255);
            $table->integer('unit')->nullable();
            $table->decimal('kelulusan_quantity', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kelulusan_kadar', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kelulusan_jumlah', $precision = 20, $scale = 2)->nullable();
            $table->decimal('terdah_quantity', $precision = 20, $scale = 2)->nullable();
            $table->decimal('terdah_jumlah', $precision = 20, $scale = 2)->nullable();
            $table->decimal('semasa_quantity', $precision = 20, $scale = 2)->nullable();
            $table->decimal('semasa_jumlah', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kumulatif_quantity', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kumulatif_jumlah', $precision = 20, $scale = 2)->nullable();
            $table->decimal('baki', $precision = 20, $scale = 2)->nullable();
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
        Schema::dropIfExists('perunding_kewangan_sub_sub_perkara');
    }
};
