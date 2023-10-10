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
        Schema::create('master_peranan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peranan',100);
            $table->string('peranan_desc',200)->nullable();
            $table->boolean('penyedia')->default(0);
            $table->boolean('penyemak_1')->default(0);
            $table->boolean('penyemak_2')->default(0);
            $table->boolean('pengesah')->default(0);
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
        Schema::dropIfExists('master_peranan');
    }
};
