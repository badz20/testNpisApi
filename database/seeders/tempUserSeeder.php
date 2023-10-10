<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class tempUserSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT temp_users ON');
        //DB::table('temp_users')->truncate();
        $json = file_get_contents(storage_path('/data/temp_users.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['temp_users'] as $datum) {
            \App\Models\tempUser::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT temp_users OFF');
        Schema::enableForeignKeyConstraints();
    }
}
