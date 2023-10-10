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
        Schema::create('rp_permohonans', function (Blueprint $table) {
            $table->id();
            $table->string('tajuk', 255)->nullable();
            $table->date('tarikh_permohonan')->nullable();
            $table->string('kos',255)->nullable();
            $table->string('bkor_catatan',255)->nullable();
            $table->string('workflow',255)->nullable();
            $table->string('status',255)->nullable();
            $table->boolean('is_first')->default(1);
            $table->text('rumusan_permohonan')->nullable();
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
        Schema::dropIfExists('rp_permohonans');
    }
};
