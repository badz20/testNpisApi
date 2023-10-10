<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MasterPerananSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT master_peranan ON');
        //DB::table('master_peranan')->truncate();
        $json = file_get_contents(storage_path('/data/master_peranan.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['master_peranan'] as $datum) {
            \App\Models\MasterPeranan::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT master_peranan OFF');
        Schema::enableForeignKeyConstraints();
    }
}
