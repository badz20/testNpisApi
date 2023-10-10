<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refJabatan extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT ref_jabatan ON');
        //DB::table('ref_jabatan')->truncate();
        $json = file_get_contents(storage_path('/data/ref_jabatan.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_jabatan'] as $datum) {
            \App\Models\refJabatan::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_jabatan OFF');
        Schema::enableForeignKeyConstraints();
    }
}
