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
        Schema::table('users', function (Blueprint $table) {
            
            $table->foreignId('pajabat_id')
                    ->after('daerah_id')
                   ->nullable()->constrained('pejabat_projek');

            $table->foreignId('kementerian_id')
                   ->after('pajabat_id')
                  ->nullable()->constrained('ref_kementerian');

            $table->boolean('is_superadmin')
                  ->after('kementerian_id')
                  ->default(0);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
