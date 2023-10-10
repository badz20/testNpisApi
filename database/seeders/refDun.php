<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refDun extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_dun ON');
        //DB::table('ref_dun')->truncate();
        $json = file_get_contents(storage_path('/data/ref_dun.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_dun'] as $datum) {
            \App\Models\refDun::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_dun OFF');
        Schema::enableForeignKeyConstraints();
    }
}
