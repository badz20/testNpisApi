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
        Schema::create('pemantauan_kewangan_skop_komponen', function (Blueprint $table) {
            $table->id();
            $table->integer('skop_id');
            $table->string('skop_project_code',50);
            $table->string('sub_skop_project_code',50);
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->string('nama_componen',100);
            $table->decimal('jumlahkos', $precision = 20, $scale = 2);
            $table->boolean('is_parent')->default(1);
            $table->integer('Kuantiti')->nullable();
            $table->string('units', 50)->nullable();
            $table->decimal('Kos', $precision = 20, $scale = 2)->nullable();
            $table->string('lain_lain',200)->nullable();
            $table->string('Catatan',100)->nullable();
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
        Schema::dropIfExists('pemantauan_kewangan_skop_komponen');
    }
};
