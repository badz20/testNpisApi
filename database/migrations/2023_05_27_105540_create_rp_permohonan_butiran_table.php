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
        Schema::create('rp_permohonan_butirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rp_permohonan_id')->constrained('rp_permohonans');
            $table->integer('order_no')->nullable();
            $table->string('tajuk', 255)->nullable();
            $table->date('tarik_surat')->nullable();
            $table->string('no_rujukan', 255)->nullable();
            $table->string('jenis_permohonan', 255)->nullable();
            $table->string('butiran_permohona', 255)->nullable();
            $table->string('image_keterangan', 255)->nullable();
            $table->string('image_name', 255)->nullable();
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
        Schema::dropIfExists('rp_permohonan_butirans');
    }
};
