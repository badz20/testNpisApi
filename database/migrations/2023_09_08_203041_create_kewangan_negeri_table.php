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
        Schema::create('kewangan_negeri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pp_id')->constrained('projects');
            $table->integer('negeri_id')->nullable();
            $table->decimal('kos_data',$precision = 18, $scale = 0);
            $table->decimal('siling_yr1',$precision = 18, $scale = 0);
            $table->decimal('siling_yr2',$precision = 18, $scale = 0);
            $table->decimal('siling_yr3',$precision = 18, $scale = 0);
            $table->decimal('siling_yr4',$precision = 18, $scale = 0);
            $table->decimal('siling_yr5',$precision = 18, $scale = 0);
            $table->decimal('siling_yr6',$precision = 18, $scale = 0);
            $table->decimal('siling_yr7',$precision = 18, $scale = 0);
            $table->decimal('siling_yr8',$precision = 18, $scale = 0);
            $table->decimal('siling_yr9',$precision = 18, $scale = 0);
            $table->decimal('siling_yr10',$precision = 18, $scale = 0);
            $table->integer('dibuat_oleh');
            $table->dateTime('dibuat_pada')->useCurrent();
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
        Schema::dropIfExists('kewangan_negeri');
    }
};
