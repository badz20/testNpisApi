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
        Schema::create('maklumat_keewangan', function (Blueprint $table) {
            $table->string('kod_projek',50)->nullable();
            $table->decimal('kos_keseluruhan', $precision = 20, $scale = 2)->nullable();
            $table->integer('tahun_kewangan');
            $table->dateTime('dijana_oleh_tarikh_jana')->useCurrent();
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
        Schema::dropIfExists('maklumat_keewangan');
    }
};
