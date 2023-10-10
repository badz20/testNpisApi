<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
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
        DB::unprepared('SET IDENTITY_INSERT roles ON');
        //DB::table('users')->truncate();
        $json = file_get_contents(storage_path('/data/roles.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['roles'] as $datum) {
            \App\Models\Role::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT roles OFF');
        Schema::enableForeignKeyConstraints();
    }
}
