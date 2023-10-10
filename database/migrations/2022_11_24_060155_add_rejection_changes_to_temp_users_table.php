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
        Schema::table('temp_users', function (Blueprint $table) {
            $table->tinyInteger('count')
                    ->default(0)
                    ->after('status_pengguna_id')
                    ->nullable();
            
            $table->tinyInteger('UpdateCounter')
                    ->default(0)
                    ->after('count')
                    ->nullable();

            $table->string('alasan_penolakan1')
                    ->after('UpdateCounter')
                    ->nullable();
            
            $table->string('alasan_penolakan2')
                    ->after('alasan_penolakan1')
                    ->nullable();
            
            $table->string('alasan_penolakan3')
                    ->after('alasan_penolakan2')
                    ->nullable();
            
            $table->string('alasan_penolakan_permanet')
                    ->after('alasan_penolakan3')
                    ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_users', function (Blueprint $table) {
            //
        });
    }
};
