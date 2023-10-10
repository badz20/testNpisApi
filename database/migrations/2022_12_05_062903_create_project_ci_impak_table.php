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
        Schema::create('project_ci_impak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('keterangan',500)->nullable();
            $table->decimal('kuantiti',$precision = 38, $scale = 2)->default(0);
            $table->decimal('nilai',$precision = 38, $scale = 2)->default(0);
            $table->string('penerangan',500)->nullable();
            $table->decimal('jangka_masa_impak',$precision = 20, $scale = 2)->default(0);
            $table->decimal('jumlah_impak',$precision = 38, $scale = 2)->default(0);
            $table->integer('dibuat_oleh')->nullable();
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
        Schema::dropIfExists('project_ci_impak');
    }
};
