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
        //
        Schema::create('vm_butiran_fasilitators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('butiran_id');
            $table->foreign('butiran_id')->references('id')->on('vm_butirans');
            $table->unsignedBigInteger('fasilitator_id');
            $table->foreign('fasilitator_id')->references('id')->on('pemantauan_fasilitator');
            $table->string('fasilitator_type', 255);
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
        //
        Schema::dropIfExists('vm_butiran_fasilitators');
    }
};
