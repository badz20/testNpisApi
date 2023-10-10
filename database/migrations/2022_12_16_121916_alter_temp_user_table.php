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
            
            $table->foreignId('pajabat_id')
                    ->after('daerah_id')
                   ->nullable()->constrained('pejabat_projek');

            $table->foreignId('kementerian_id')
                   ->after('pajabat_id')
                  ->nullable()->constrained('ref_kementerian');

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
