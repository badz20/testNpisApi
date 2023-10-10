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
        Schema::create('projek_kewangan_maklumat_peruntukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_projek_id')->constrained('projects');
            $table->integer('perkra_id')->nullable();
            $table->decimal('year1',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year2',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year3',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year4',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year5',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year6',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year7',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year8',$precision = 18, $scale = 0)->nullable();
            $table->decimal('year9',$precision = 18, $scale = 0)->nullable();  
            $table->decimal('year10',$precision = 18, $scale = 0)->nullable();
            $table->decimal('jumlah_kos',$precision = 18, $scale = 0)->nullable();
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
        Schema::dropIfExists('projek_kewangan_maklumat_peruntukan');
    }
};
