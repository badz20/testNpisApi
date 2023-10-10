<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BriefProjekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $json = file_get_contents(storage_path('/data/brief_projek.json'));
        $json_data = json_decode($json,true); 
        
        foreach ($json_data['brif_projek'] as $datum) { 
            \App\Models\Brief_projek::create($datum);
        }

        Schema::enableForeignKeyConstraints();
    }
}
