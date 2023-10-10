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
        Schema::create('lookups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->string('code')->nullable();
            $table->json('json_value');            
            $table->foreignId('dikemaskini_oleh')->nullable();
            $table->string('catatan')->nullable();
            $table->boolean('row_status')->default(1);
            $table->bigInteger('order_by')->nullable();            
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
        Schema::dropIfExists('lookup');
    }
};
