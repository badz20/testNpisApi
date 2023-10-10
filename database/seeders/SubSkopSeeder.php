<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SubSkopSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT sub_skop_options ON');
        //DB::table('ref_sektor')->truncate();
        $json = file_get_contents(storage_path('/data/sub_skop_options.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['sub_skop_options'] as $datum) {
            \App\Models\SubSkopOption::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT sub_skop_options OFF');
        Schema::enableForeignKeyConstraints();
    }
}
