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
        Schema::create('cms_footer', function (Blueprint $table) {
            $table->id();
            $table->string('footer_pautan_logo_1',100);
            $table->string('footer_gambar_latarbelakang',100);
            $table->string('footer_hakcipta',500);
            $table->string('bilangan_pelawat',150);
            $table->string('bilangan pelawat_Hari_Ini',150);
            $table->string('bilangan_Pelawat_Bulan_Ini',150);
            $table->string('bilangan_Pelawat_Tahun _ini',150);
            
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
        Schema::dropIfExists('cms_footer');
    }
};
