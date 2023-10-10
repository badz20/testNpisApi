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
        Schema::create('projek_kewangan_kategori_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_projek_id')->constrained('projects');
            $table->string('kategori_nama',100)->nullable();
            $table->decimal('kategori_1_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_2_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_3_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_4_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_5_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_6_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_7_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_8_yr',$precision = 18, $scale = 0)->nullable();
            $table->decimal('kategori_9_yr',$precision = 18, $scale = 0)->nullable();  
            $table->decimal('kategori_10_yr',$precision = 18, $scale = 0)->nullable();
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
        Schema::dropIfExists('projek_kewangan_kategori_belanja');
    }
};
