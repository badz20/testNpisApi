<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refUnit extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT REF_Unit ON');
        //DB::table('REF_Unit')->truncate();
        $json = file_get_contents(storage_path('/data/REF_Unit.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['REF_Unit'] as $datum) {
            \App\Models\OutputUnit::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT REF_Unit OFF');
        Schema::enableForeignKeyConstraints();
    }
}
