<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BahagianEpuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        DB::unprepared('SET IDENTITY_INSERT ref_bahagian_epu_jpm ON');
        //DB::table('ref_bahagian_epu_jpm')->truncate();
        $json = file_get_contents(storage_path('/data/ref_bahagian_epu_jpm.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_bahagian_epu_jpm'] as $datum) {
            \App\Models\BahagianEpuJpm::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_bahagian_epu_jpm OFF');
        Schema::enableForeignKeyConstraints();

    }

}
