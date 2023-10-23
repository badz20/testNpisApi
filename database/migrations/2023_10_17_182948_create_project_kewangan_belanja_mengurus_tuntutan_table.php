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
        Schema::create('project_kewangan_belanja_mengurus_tuntutan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('belanja_mengurus_id')->constrained('project_kewangan_belanja_mengurus');
            
            $table->decimal('anggaran_perjalanan',$precision = 38, $scale = 2)->default(0);
            $table->decimal('mesyuarat_tapak',$precision = 38, $scale = 2)->default(0);
            $table->decimal('mesyuarat_teknikal',$precision = 38, $scale = 2)->default(0);            
            $table->decimal('mesyuarat_pemantauan',$precision = 38, $scale = 2)->default(0);
            $table->decimal('mesyuarat_kemajuan_perunding',$precision = 38, $scale = 2)->default(0);

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
        Schema::dropIfExists('project_kewangan_belanja_mengurus_tuntutan');
    }
};
