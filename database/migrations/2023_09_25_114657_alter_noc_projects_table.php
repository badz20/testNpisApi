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
        Schema::table('noc_project', function (Blueprint $table) {
            $table->text('justifikasi')->nullable();
            $table->string('lampiran_file_name', 200)->nullable();
            $table->string('memo_file_name', 200)->nullable();
            $table->text('penerangan')->nullable();
            $table->string('butiran_code', 200)->nullable();
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
    }
};
