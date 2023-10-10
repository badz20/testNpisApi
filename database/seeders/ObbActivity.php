<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ObbActivity extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Schema::disableForeignKeyConstraints();
        DB::unprepared('SET IDENTITY_INSERT ref_rkm_obb_aktivity ON');
        //DB::table('ref_rkm_obb_aktivity')->truncate();
        $json = file_get_contents(storage_path('/data/ref_rkm_obb_aktivity.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_rkm_obb_aktivity'] as $datum) {
            \App\Models\RmkObb::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_rkm_obb_aktivity OFF');
        Schema::enableForeignKeyConstraints();
    }
}
