<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refKomponen extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT REF_Komponen ON');
        //DB::table('REF_Komponen')->truncate();
        $json = file_get_contents(storage_path('/data/REF_Komponen.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['REF_Komponen'] as $datum) {
            \App\Models\refKomponen::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT REF_Komponen OFF');
        Schema::enableForeignKeyConstraints();
    }
}
