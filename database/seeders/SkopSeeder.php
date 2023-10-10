<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SkopSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT skop_options ON');
        //DB::table('ref_sektor')->truncate();
        $json = file_get_contents(storage_path('/data/skop_options.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['skop_options'] as $datum) {
            \App\Models\SkopOption::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT skop_options OFF');
        Schema::enableForeignKeyConstraints();
    }
}
