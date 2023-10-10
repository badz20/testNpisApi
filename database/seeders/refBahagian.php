<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refBahagian extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_bahagian ON');
        //DB::table('ref_bahagian')->truncate();
        $json = file_get_contents(storage_path('/data/ref_bahagian.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_bahagian'] as $datum) {
            \App\Models\refBahagian::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_bahagian OFF');
        Schema::enableForeignKeyConstraints();
    }
}
