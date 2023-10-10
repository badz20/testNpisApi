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
        Schema::create('ref_deliverables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('heading_id');
            $table->foreign('heading_id')->references('id')->on('ref_deliverable_headings');
            $table->string('nama', 255)->nullable();
            $table->string('code', 10)->nullable();
            $table->integer('order')->nullable();
            $table->string('catatan', 255)->nullable();
            $table->integer('dibuat_oleh')->nullable();
            $table->dateTime('dibuat_pada')->useCurrent()->nullable();
            $table->integer('dikemaskini_oleh')->nullable();
            $table->dateTime('dikemaskini_pada')->nullable();
            $table->boolean('is_hidden')->default(0);
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
        Schema::dropIfExists('rp_deliverables_');
    }
};
