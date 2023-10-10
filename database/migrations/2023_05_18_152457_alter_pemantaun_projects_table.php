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
        Schema::table('pemantauan_project', function (Blueprint $table) {
            $table->integer('penjilidan_status_va')->nullable();
            $table->integer('penjilidan_status_ve')->nullable();
            $table->integer('no_bayaran')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemantauan_project', function (Blueprint $table) {
            $table->dropColumn('penjilidan_status_va');
            $table->dropColumn('penjilidan_status_ve');
            $table->dropColumn('no_bayaran');
        });
    }
};
