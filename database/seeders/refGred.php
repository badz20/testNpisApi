<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refGred extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_gred ON');
        //DB::table('ref_gred')->truncate();
        $json = file_get_contents(storage_path('/data/ref_gred.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_gred'] as $datum) {
            \App\Models\refGredJawatan::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_gred OFF');
        Schema::enableForeignKeyConstraints();
    }
}
