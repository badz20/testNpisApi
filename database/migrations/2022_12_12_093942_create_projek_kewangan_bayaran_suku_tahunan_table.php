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
        Schema::create('projek_kewangan_bayaran_suku_tahunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_projek_id')->constrained('projects')->name('kewangan_bayaran_projek_id_foreign');
            $table->integer('skop_id')->nullable();
            $table->string('yr1_quarters1',10);
            $table->string('yr1_quarters2',10);
            $table->string('yr1_quarters3',10);
            $table->string('yr1_quarters4',10);
            $table->string('yr1_quarters5',10);
            $table->string('yr1_quarters6',10);
            $table->string('yr1_quarters7',10);
            $table->string('yr1_quarters8',10);
            $table->string('yr1_quarters9',10);
            $table->string('yr1_quarters10',10);
            $table->string('yr1_quarters11',10);
            $table->string('yr1_quarters12',10);
            $table->string('yr1_quarters13',10);
            $table->string('yr1_quarters14',10);
            $table->string('yr1_quarters15',10);
            $table->string('yr1_quarters16',10);
            $table->string('yr1_quarters17',10);
            $table->string('yr1_quarters18',10);
            $table->string('yr1_quarters19',10);
            $table->string('yr1_quarters20',10);
            $table->string('yr1_quarters21',10);
            $table->string('yr1_quarters22',10);
            $table->string('yr1_quarters23',10);
            $table->string('yr1_quarters24',10);
            $table->string('yr1_quarters25',10);
            $table->string('yr1_quarters26',10);
            $table->string('yr1_quarters27',10);
            $table->string('yr1_quarters28',10);
            $table->string('yr1_quarters29',10);
            $table->string('yr1_quarters30',10);
            $table->string('yr1_quarters31',10);
            $table->string('yr1_quarters32',10);
            $table->string('yr1_quarters33',10);
            $table->string('yr1_quarters34',10);
            $table->string('yr1_quarters35',10);
            $table->string('yr1_quarters36',10);
            $table->string('yr1_quarters37',10);
            $table->string('yr1_quarters38',10);
            $table->string('yr1_quarters39',10);
            $table->string('yr1_quarters40',10);
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
        Schema::dropIfExists('projek_kewangan_bayaran_suku_tahunan');
    }
};
