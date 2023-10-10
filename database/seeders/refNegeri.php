<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refNegeri extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_negeri ON');
        //\App\Models\refNegeri::truncate();
        $json = file_get_contents(storage_path('/data/ref_negeri.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_negeri'] as $datum) {
            \App\Models\refNegeri::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_negeri OFF');
        Schema::enableForeignKeyConstraints();
    }
}
