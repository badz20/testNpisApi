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
        Schema::create('rp_permohonan_bahagians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rp_permohonan_id')->constrained('rp_permohonans');
            $table->foreignId('bahagian_id')->constrained('ref_bahagian');
            $table->string('status',100)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('row_status')->default(1);
            $table->text('bkor_catatan')->nullable();
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
        Schema::dropIfExists('rp_permohonan_bahagians');
    }
};
