<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rp_sejarah_bahagians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rp_permohonan_id')->constrained('rp_permohonans');
            $table->date('tarikh_maklumbalas');
            $table->string('isu', 255)->nullable();
            $table->string('ulasan_teknical', 255)->nullable();
            $table->string('cadangan_jangka_pendek', 255)->nullable();
            $table->string('cadangan_jangka_panjang', 255)->nullable();
            $table->string('lampiran', 255)->nullable();
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
        Schema::dropIfExists('rp_sejarah_bahagian');
    }
};
