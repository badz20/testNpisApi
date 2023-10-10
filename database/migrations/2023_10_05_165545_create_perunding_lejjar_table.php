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
        Schema::create('perunding_lejjar', function (Blueprint $table) {
            $table->id();
            $table->integer('no_bayaran');
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan');
            $table->foreign('perolehan')->references('id')->on('pemantauan_perolehans');
            $table->decimal('yuran_perunding', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('inbuhan_balik', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('jps_yuran_perunding', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('jps_inbuhan_balik', $precision = 20, $scale = 2)->default('0.00');
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
        Schema::dropIfExists('perunding_lejjar');
    }
};
