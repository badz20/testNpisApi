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
        Schema::create('yuran_perunding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan');
            $table->foreign('perolehan')->references('id')->on('pemantauan_perolehans');
            $table->integer('no_bayaran');
            $table->text('perjanjian_text');
            $table->decimal('perjanjian', $precision = 20, $scale = 2)->nullable();
            $table->decimal('bayaran_terdhulu', $precision = 20, $scale = 2)->nullable();
            $table->decimal('tututan_terkini', $precision = 20, $scale = 2)->nullable();
            $table->decimal('kumulatif', $precision = 20, $scale = 2)->nullable();
            $table->decimal('cukai_tamba', $precision = 20, $scale = 2)->nullable();
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
        Schema::dropIfExists('yuran_perunding');
    }
};
