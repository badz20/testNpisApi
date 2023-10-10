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
        Schema::create('noc_project', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->string('kod_projeck',50)->nullable();
            $table->decimal('kos_projeck', $precision = 20, $scale = 2)->nullable();
            $table->text('skop')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('komponen')->nullable();
            $table->string('nama_projek',1000)->nullable();
            $table->text('objektif')->nullable();
            $table->string('no_rujukan',50)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();  
            $table->integer('status_id');
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
        Schema::dropIfExists('noc_project');
    }
};
