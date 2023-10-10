<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class refIndikator extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Schema::disableForeignKeyConstraints();
        // DB::unprepared('SET IDENTITY_INSERT REF_Indikatori ON');
        // //DB::table('REF_Indikatori')->truncate();
        // $json = file_get_contents(storage_path('/data/REF_Indikatori.json'));
        // $json_data = json_decode($json,true);

        // foreach ($json_data['REF_Indikatori'] as $datum) {
        //     \App\Models\RmkIndikatori::create($datum);
        // }
        // DB::unprepared('SET IDENTITY_INSERT REF_Indikatori OFF');
        // Schema::enableForeignKeyConstraints();

        Schema::disableForeignKeyConstraints();
        //DB::unprepared('SET IDENTITY_INSERT REF_Indikatori ON');

        DB::unprepared(file_get_contents(storage_path('/data/REF_Indikatori.sql')));

        //DB::unprepared('SET IDENTITY_INSERT REF_Indikatori OFF');
        Schema::enableForeignKeyConstraints();
    }
}
