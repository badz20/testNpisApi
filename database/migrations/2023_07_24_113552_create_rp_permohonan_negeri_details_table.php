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
        Schema::create('rp_permohonan_negeri_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rp_permohonan_id')->constrained('rp_permohonans');
            $table->foreignId('bahagian_id')->constrained('rp_permohonan_bahagian_details')->onDelete('cascade');
            $table->boolean('is_dimohon')->default(0)->nullable();
            $table->string('no_rujukan', 255)->nullable();
            $table->string('isu', 255)->nullable();
            $table->string('ulasan_teknikal', 255)->nullable();
            $table->string('cadagan_jangka_pendek', 255)->nullable();
            $table->string('cadagan_jangka_panjang', 255)->nullable();
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
        Schema::dropIfExists('rp_permohonan_negeri_details');
    }
};
