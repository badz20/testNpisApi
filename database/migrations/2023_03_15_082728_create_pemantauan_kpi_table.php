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
        Schema::create('pemantauan_kpi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->decimal('kuantiti',$precision = 18, $scale = 0);
            $table->string('unit',100);
            $table->string('penerangan',200);
            $table->integer('yr_1')->nullable();
            $table->integer('yr_2')->nullable();
            $table->integer('yr_3')->nullable();
            $table->integer('yr_4')->nullable();
            $table->integer('yr_5')->nullable();
            $table->integer('yr_6')->nullable();
            $table->integer('yr_7')->nullable();
            $table->integer('yr_8')->nullable();
            $table->integer('yr_9')->nullable();
            $table->integer('yr_10')->nullable();
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
        Schema::dropIfExists('pemantauan_kpi');
    }
};
