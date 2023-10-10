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
        Schema::create('lokaliti', function (Blueprint $table) {
            $table->string('kod_projek',50)->nullable();
            $table->unsignedBigInteger('negeri')->nullable();
            $table->foreign('negeri')->references('id')->on('ref_negeri');
            $table->unsignedBigInteger('daerah')->nullable();
            $table->foreign('daerah')->references('id')->on('ref_daerah');
            $table->unsignedBigInteger('parlimen')->nullable();
            $table->foreign('parlimen')->references('id')->on('ref_parliment');
            $table->unsignedBigInteger('dun')->nullable();
            $table->foreign('dun')->references('id')->on('ref_dun');
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
        Schema::dropIfExists('lokaliti');
    }
};
