<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UserTypesSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT user_types ON');
        //DB::table('users')->truncate();
        $json = file_get_contents(storage_path('/data/user_types.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['user_types'] as $datum) {
            \App\Models\UserType::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT user_types OFF');
        Schema::enableForeignKeyConstraints();
    }
}
