<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SektorUtama extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_sektor_utama ON');
        //DB::table('ref_sektor_utama')->truncate();
        $json = file_get_contents(storage_path('/data/ref_sektor_utama.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_sektor_utama'] as $datum) {
            \App\Models\SektorUtama::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_sektor_utama OFF');
        Schema::enableForeignKeyConstraints();
    }
}
