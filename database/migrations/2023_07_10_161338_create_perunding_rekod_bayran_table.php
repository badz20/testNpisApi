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
        Schema::create('perunding_rekod_bayran', function (Blueprint $table) {
            $table->id();
            $table->integer('no_bayaran');
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan');
            $table->foreign('perolehan')->references('id')->on('pemantauan_perolehans');
            $table->decimal('yuran_perunding', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('inbuhan_balik', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('cukai_perkhidmatan', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('jumlah_bayaran', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('lad_value', $precision = 20, $scale = 2)->default('0.00');
            $table->decimal('penjanjian_asal', $precision = 20, $scale = 2)->default('0.00');
            $table->date('tarik_baucer')->nullable();
            $table->string('no_baucer')->default('0');
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
        Schema::dropIfExists('perunding_rekod_bayran');
    }
};
