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
        Schema::create('rp_permohonan_negeris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rp_permohonan_id')->constrained('rp_permohonans')->nullable();
            $table->integer('order_no')->nullable();
            $table->foreignId('negeri_id')->constrained('ref_negeri')->nullable();
            $table->foreignId('daerah_id')->constrained('ref_daerah')->nullable();
            $table->foreignId('parlimen_id')->constrained('ref_parliment')->nullable();
            $table->foreignId('dun_id')->constrained('ref_dun')->nullable();
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
        Schema::dropIfExists('rp_permohonan_negeris');
    }
};
