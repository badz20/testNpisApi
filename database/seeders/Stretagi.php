<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Stretagi extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT REF_Strategi ON');
        //DB::table('REF_Strategi')->truncate();
        $json = file_get_contents(storage_path('/data/REF_Strategi.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['REF_Strategi'] as $datum) {
            \App\Models\RmkStrategi::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT REF_Strategi OFF');
        Schema::enableForeignKeyConstraints();
    }
}
