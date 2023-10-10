<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT users ON');
        //DB::table('users')->truncate();
        $json = file_get_contents(storage_path('/data/users.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['users'] as $datum) {
            \App\Models\User::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT users OFF');
        Schema::enableForeignKeyConstraints();
    }
}
