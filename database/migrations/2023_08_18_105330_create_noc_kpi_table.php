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
        Schema::create('noc_kpi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->foreign('pp_id')->references('id')->on('pemantauan_project');
            $table->integer('noc_id')->nullable();
            $table->string('no_rujukan',50)->nullable();
            $table->bigInteger('project_id');
            $table->decimal('kuantiti',$precision = 18, $scale = 0);
            $table->integer('unit');
            $table->text('penerangan');
            $table->decimal('yr_1', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_2', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_3', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_4', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_5', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_6', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_7', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_8', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_9', $precision = 18, $scale = 2)->nullable();
            $table->decimal('yr_10', $precision = 18, $scale = 2)->nullable();
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
        Schema::dropIfExists('noc_kpi');
    }
};
