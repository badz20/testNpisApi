<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refDaerah extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_daerah ON');
        //DB::table('ref_daerah')->truncate();
        $json = file_get_contents(storage_path('/data/ref_daerah.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_daerah'] as $datum) {
            \App\Models\refDaerah::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_daerah OFF');
        Schema::enableForeignKeyConstraints();
    }
}
