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
        Schema::create('vm_ulasan_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('ulasan_id');
            $table->foreign('ulasan_id')->references('id')->on('vm_ulasans');
            $table->string('perkara',255)->nullable();
            $table->string('catatan',255)->nullable();
            $table->boolean('is_complete');
            $table->boolean('is_submitted');
            $table->string('status',255);
            $table->date('tarikh_hantar');
            $table->date('tarikh_maklumbalas')->nullable();
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
        Schema::dropIfExists('vm_ulasan_histories');
    }
};
