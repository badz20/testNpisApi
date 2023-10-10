<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModelHasRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //
         $json = file_get_contents(storage_path('/data/model_has_role.json'));
         $json_data = json_decode($json,true);
         
         foreach ($json_data['model_has_role'] as $datum) { 
             \App\Models\ModelHasRole::create($datum);
         }

    }
}
