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
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->decimal('kuantiti', 18, 2)->change();
            $table->decimal('yr_1', 18, 2)->change()->nullable();
            $table->decimal('yr_2', 18, 2)->change()->nullable();
            $table->decimal('yr_3', 18, 2)->change()->nullable();
            $table->decimal('yr_4', 18, 2)->change()->nullable();
            $table->decimal('yr_5', 18, 2)->change()->nullable();
            $table->decimal('yr_6', 18, 2)->change()->nullable();
            $table->decimal('yr_7', 18, 2)->change()->nullable();
            $table->decimal('yr_8', 18, 2)->change()->nullable();
            $table->decimal('yr_9', 18, 2)->change()->nullable();
            $table->decimal('yr_10', 18, 2)->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->decimal('kuantiti', 18, 2)->change();

        });
    }
};
