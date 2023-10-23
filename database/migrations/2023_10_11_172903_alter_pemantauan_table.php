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
        Schema::table('pemantauan_project', function (Blueprint $table) {

            $table->decimal('keperluan_jps',$precision = 18, $scale = 0)->nullable();
            $table->decimal('peruntukan_asal',$precision = 18, $scale = 0)->nullable();
            $table->decimal('tambahan',$precision = 18, $scale = 0)->nullable();
            $table->decimal('pemulangan',$precision = 18, $scale = 0)->nullable();
            $table->decimal('peruntukan_dipinda',$precision = 18, $scale = 0)->nullable();
            $table->boolean('jps_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemantauan_project', function (Blueprint $table) {
            $table->dropColumn('keperluan_jps');
            $table->dropColumn('peruntukan_asal');
            $table->dropColumn('tambahan');
            $table->dropColumn('pemulangan');
            $table->dropColumn('peruntukan_dipinda');
            $table->dropColumn('jps_status');
        });
    }
};
