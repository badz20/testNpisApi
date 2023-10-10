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
        Schema::create('ref_sub_sektor', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kod_sub_sektor',50);
            $table->string('penerangan_sub_sektor',500)->nullable();
            $table->foreignId('bahagian_id')->constrained('ref_bahagian_epu_jpm');
            $table->foreignId('sektor_utama_id')->constrained('ref_sektor_utama');
            $table->foreignId('sektor_id')->constrained('ref_sektor');
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
        Schema::dropIfExists('ref_sub_sektor');
    }
};
