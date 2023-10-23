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
        Schema::create('project_kewangan_belanja_mengurus_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('belanja_mengurus_id')->constrained('project_kewangan_belanja_mengurus');
            $table->string('type',100);
            $table->decimal('nilai1',$precision = 38, $scale = 2)->default(0);
            $table->string('unit',255)->default(0);
            $table->decimal('rm',$precision = 38, $scale = 2)->default(0);
            $table->string('kadar_unit',255)->default(0);
            $table->decimal('nilai2',$precision = 38, $scale = 2)->default(0);
            $table->string('kali',255)->default(0);
            $table->decimal('jumlah',$precision = 38, $scale = 2)->default(0);
            $table->decimal('jumlahRMK',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_1',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_2',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_3',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_4',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_5',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_6',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_7',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_8',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_9',$precision = 38, $scale = 2)->default(0);
            $table->decimal('yr_10',$precision = 38, $scale = 2)->default(0);
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
        Schema::dropIfExists('project_kewangan_belanja_mengurus_details');
    }
};