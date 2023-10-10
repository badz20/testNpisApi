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
        Schema::create('noc_pindan', function (Blueprint $table) {
            $table->id();
            $table->integer('noc_id');
            $table->integer('pp_id');
            $table->string('lampiran_pindan_file_name', 255)->nullable();
            $table->integer('agensi');
            $table->date('maklumat_pindan_date')->nullable();
            $table->text('ringasakan_ulasan')->nullable();
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
        Schema::dropIfExists('noc_pindan');
    }
};
