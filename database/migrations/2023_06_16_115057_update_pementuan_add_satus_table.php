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
            $table->integer('va_status')->nullable();
            $table->integer('ve_status')->nullable();
            $table->integer('vr_status')->nullable();
            $table->integer('current_status')->nullable();
            $table->integer('noc_status')->nullable();
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
            $table->dropColumn('va_status');
            $table->dropColumn('ve_status');
            $table->dropColumn('vr_status');
            $table->dropColumn('current_status');
            $table->dropColumn('noc_status');
        });
    }
};
