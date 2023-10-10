<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaklumatKeewanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $json = file_get_contents(storage_path('/data/maklumat_keewangan.json'));
        $json_data = json_decode($json,true); 
        
        foreach ($json_data['maklumat_keewangan'] as $datum) { 
            \App\Models\Maklumat_keewangan::create($datum);
        }

        Schema::enableForeignKeyConstraints();
    }
}
