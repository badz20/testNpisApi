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
        Schema::create('ref_bahagian_epu_jpm', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kod_bahagian',50)->unique();
            $table->string('penerangan_bahagian',500)->nullable();
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
        Schema::dropIfExists('ref_bahagian_epu_jpm');
    }
};
