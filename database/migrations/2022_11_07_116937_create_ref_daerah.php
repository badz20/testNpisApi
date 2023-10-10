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
        Schema::create('ref_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kod_daerah',50);
            $table->string('kod_negeri',50);
            $table->string('nama_daerah',100);
            $table->string('penerangan_daerah',500)->nullable();
            $table->foreignId('negeri_id')->constrained('ref_negeri');
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->boolean('row_status')->nullable();
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
        Schema::dropIfExists('ref_daerah');
    }
};
