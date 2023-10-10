<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refParlimen extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_parliment ON');
        //DB::table('ref_parliment')->truncate();
        $json = file_get_contents(storage_path('/data/ref_parliment.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_parliment'] as $datum) {
            \App\Models\refParlimen::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_parliment OFF');
        Schema::enableForeignKeyConstraints();
    }
}
