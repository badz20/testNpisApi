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
        Schema::table('notification', function (Blueprint $table) {
            $table->integer('notification_type')->nullable();
            $table->string('notification_sub_type',500)->nullable();
            $table->integer('negeri_id')->nullable();
            $table->integer('bahagian_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropColumn('notification_type');
            $table->dropColumn('notification_sub_type');
            $table->dropColumn('negeri_id');
            $table->dropColumn('bahagian_id');
        });
    }
};
