<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RMKSDG extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT REF_RMK_SDG ON');
        //DB::table('REF_RMK_SDG')->truncate();
        $json = file_get_contents(storage_path('/data/REF_RMK_SDG.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['REF_RMK_SDG'] as $datum) {
            \App\Models\RmkSdg::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT REF_RMK_SDG OFF');
        Schema::enableForeignKeyConstraints();
    }
}
