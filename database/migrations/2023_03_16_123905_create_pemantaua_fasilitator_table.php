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
        Schema::create('pemantauan_fasilitator', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitator',1000);
            $table->string('tugas',50)->nullable();
            $table->integer('tugas_id')->nullable();
            $table->integer('bahagian_id')->nullable();
            $table->foreignId('jawatan_id')->nullable()->constrained('ref_jawatan');
            $table->foreignId('gred_id')->nullable()->constrained('ref_gred');
            $table->smallInteger('fasilitator_type');
            $table->foreignId('jabatan_id')->nullable()->constrained('ref_jabatan');
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent()->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('row_status')->default(1);
            $table->boolean('IsActive')->default(1);
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
        Schema::dropIfExists('pemantauan_fasilitator');
    }
};
