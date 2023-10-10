<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refMukim extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_mukim ON');
        //DB::table('ref_mukim')->truncate();
        $json = file_get_contents(storage_path('/data/ref_mukim.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_mukim'] as $datum) {
            \App\Models\refMukim::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_mukim OFF');
        Schema::enableForeignKeyConstraints();
    }
}
