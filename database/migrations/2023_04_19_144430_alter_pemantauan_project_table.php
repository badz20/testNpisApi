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
            $table->boolean('Is_changed_to_va')->default(0);
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
            $table->dropColumn('Is_changed_to_va');
        });
    }
};
