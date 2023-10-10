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
        //
        Schema::table('projects', function (Blueprint $table) {
            
            $table->boolean('is_bahagian_terlibat')->default(0);
            $table->string('reka_bantuk_siap',10)->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
