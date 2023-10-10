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
        Schema::create('perunding_maklumat_sas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemantauan_id');
            $table->foreign('pemantauan_id')->references('id')->on('pemantauan_project');
            $table->unsignedBigInteger('perolehan_id');
            $table->foreign('perolehan_id')->references('id')->on('pemantauan_perolehans');
            $table->unsignedBigInteger('maklumat_id');
            $table->foreign('maklumat_id')->references('id')->on('perunding_maklumats');
            $table->date('tarikh')->nullable();
            $table->decimal('implikasi_kos', $precision = 20, $scale = 2)->default(0);
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
        Schema::dropIfExists('perunding_maklumat_sas');
    }
};