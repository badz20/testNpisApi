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
        Schema::create('pentadbir_map_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('modul_id');
            $table->string('nama_servis',200)->nullable();
            $table->string('pautan_api',150)->nullable();
            $table->string('server',100);
            $table->boolean('status')->default(1);
            $table->dateTime('status_update_on')->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent()->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('row_status')->default(1);
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pentadbir_map_services');
    }
};
