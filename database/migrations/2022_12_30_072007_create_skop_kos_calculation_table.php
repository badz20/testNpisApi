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
        Schema::create('skop_kos_calculation', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_cost', $precision = 20, $scale = 2)->nullable();
            $table->decimal('P_min', $precision = 20, $scale = 2)->nullable();
            $table->decimal('P_max', $precision = 20, $scale = 2)->nullable();
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
        Schema::dropIfExists('skop_kos_calculation');
    }
};
