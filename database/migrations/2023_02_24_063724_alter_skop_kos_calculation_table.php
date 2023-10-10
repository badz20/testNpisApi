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
        Schema::table('skop_kos_calculation', function (Blueprint $table) {
            $table->boolean('IsActive')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skop_kos_calculation', function (Blueprint $table) {
            $table->dropColumn('IsActive');
        });
    }
};
