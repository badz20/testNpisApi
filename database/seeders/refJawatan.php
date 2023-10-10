<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refJawatan extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_jawatan ON');
        //DB::table('ref_jawatan')->truncate();
        $json = file_get_contents(storage_path('/data/ref_jawatan.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_jawatan'] as $datum) {
            \App\Models\refJawatan::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_jawatan OFF');
        Schema::enableForeignKeyConstraints();
    }
}
