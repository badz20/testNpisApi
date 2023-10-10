<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Sasaran extends Seeder
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
        //DB::unprepared('SET IDENTITY_INSERT REF_Sasaran ON');

        DB::unprepared(file_get_contents(storage_path('/data/REF_Sasaran.sql')));

        //DB::unprepared('SET IDENTITY_INSERT REF_Sasaran OFF');
        Schema::enableForeignKeyConstraints();
    }
}
