<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Sektor extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_sektor ON');
        //DB::table('ref_sektor')->truncate();
        $json = file_get_contents(storage_path('/data/ref_sektor.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_sektor'] as $datum) {
            \App\Models\Sektor::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_sektor OFF');
        Schema::enableForeignKeyConstraints();
    }
}
