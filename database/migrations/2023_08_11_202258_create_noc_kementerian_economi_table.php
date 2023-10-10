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
        Schema::create('noc_kementerian_economi', function (Blueprint $table) {
            $table->id();
            $table->integer('noc_id');
            $table->date('economi_tarikh');
            $table->string('economi_file_name', 200);
            $table->date('economi_surat_tarikh');
            $table->string('economi_surat_file_name', 200);
            $table->integer('status')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('noc_kementerian_economi');
    }
};
