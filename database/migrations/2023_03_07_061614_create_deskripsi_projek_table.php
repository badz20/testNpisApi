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
        Schema::create('deskripsi_projek', function (Blueprint $table) {
            $table->string('kod_projek',50)->nullable();
            $table->string('bayaran_akhir',100);
            $table->string('projek_one_line_item',200);
            $table->string('projek_pakej_rangsangan_ekonomi',400);
            $table->text('objektif');
            $table->text('keterangan_projek');
            $table->text('skop_projek');
            $table->integer('komponen');
            $table->string('kumpulan_sasar',200);
            $table->text('output_projek');
            $table->text('outcome_projek');
            $table->text('kpi_projek');
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
        Schema::dropIfExists('deskripsi_projek');
    }
};
