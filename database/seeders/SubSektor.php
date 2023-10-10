<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SubSektor extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_sub_sektor ON');
        //DB::table('ref_sub_sektor')->truncate();
        $json = file_get_contents(storage_path('/data/ref_sub_sektor.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_sub_sektor'] as $datum) {
            \App\Models\SubSektor::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_sub_sektor OFF');
        Schema::enableForeignKeyConstraints();
    }
}
