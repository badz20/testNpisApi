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
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('penyemak')->nullable();
            $table->string('penyemak_catatan',1000)->nullable();
            $table->dateTime('penyemak_review_date')->nullable();
            $table->integer('penyemak1_priority_order')->nullable();
            $table->integer('pengesha_priority_order')->nullable();
            $table->boolean('Is_submitted_by_penyemak1')->default(0);
            $table->boolean('Is_submitted_by_pengesha')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('penyemak');
            $table->dropColumn('penyemak_catatan');
            $table->dropColumn('penyemak_review_date');
            $table->dropColumn('penyemak1_priority_order');
            $table->dropColumn('pengesha_priority_order');
            $table->dropColumn('Is_submitted_by_penyemak1');
            $table->dropColumn('Is_submitted_by_pengesha');
        });
    }
};
