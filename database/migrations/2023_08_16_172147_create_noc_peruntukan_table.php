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
        Schema::create('noc_peruntukan', function (Blueprint $table) {
            $table->id();
            $table->string('bilangan', 200);
            $table->integer('tahun')->nullable();
            $table->date('tarikh_buka')->nullable();
            $table->date('tarikh_tutup')->nullable();
            $table->integer('status_permohonan')->nullable();
            $table->integer('status')->nullable();
            $table->boolean('type')->default(0);
            $table->boolean('active_status')->default(0);
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
        Schema::dropIfExists('noc_peruntukan');
    }
};
