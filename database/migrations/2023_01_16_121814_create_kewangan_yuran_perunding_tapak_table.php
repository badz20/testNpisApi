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
        Schema::create('Projek_Kewangan_yuran_perunding_tapak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_projek_id')->constrained('projects');
            $table->boolean('is_Profesional')->default(1);
            $table->string('jawatan',100)->nullable();
            $table->decimal('man_month', $precision = 20, $scale = 2)->nullable();
            $table->decimal('jumlahkos', $precision = 20, $scale = 2)->nullable();
            $table->decimal('multiplier', $precision = 20, $scale = 2)->nullable();
            $table->decimal('salary', $precision = 20, $scale = 2)->nullable();
            $table->decimal('amount', $precision = 20, $scale = 2)->nullable();
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
        Schema::dropIfExists('Projek_Kewangan_yuran_perunding_tapak');
    }
};
