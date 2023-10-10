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
        Schema::create('vm_butirans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->string('negeri', 255);
            $table->string('cadangan_makmal',100);
            $table->string('makmal_sebenar',100);
            $table->string('lawatan_tapak',100);
            $table->string('mesyuarat_date',100);
            $table->integer('tahun_makmal');
            $table->string('kos_sebelum_makmal',100)->nullable();
            $table->string('kos_pelakas_selepas_makmal',100);
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
        Schema::dropIfExists('vm_butirans');
    }
};
