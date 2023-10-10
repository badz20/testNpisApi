<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class MaklumatProjekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $json = file_get_contents(storage_path('/data/projek_maklumat.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['projek_maklumat'] as $datum) {
            \App\Models\Maklumat_projek::create($datum);
        }

        Schema::enableForeignKeyConstraints();
    }
}

?>
