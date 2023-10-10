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
        Schema::create('noc_skop', function (Blueprint $table) {
            $table->id();
            $table->integer('noc_id');
            $table->foreignId('skop_id')->constrained('skop_options');
            $table->integer('sub_skop_id')->nullable();
            $table->decimal('skop_kos',$precision = 18, $scale = 0);
            $table->integer('dibuat_oleh');
            $table->dateTime('dibuat_pada')->useCurrent();
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
        Schema::dropIfExists('noc_skop');
    }
};
