<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class JenisSubKategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('ref_jenis_sub_kategori')->truncate();
        Schema::disableForeignKeyConstraints();
        DB::unprepared('SET IDENTITY_INSERT ref_jenis_sub_kategori ON');
        //DB::table('ref_jenis_sub_kategori')->truncate();
        $json = file_get_contents(storage_path('/data/ref_jenis_sub_kategori.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['ref_jenis_sub_kategori'] as $datum) {
            \App\Models\JenisSubKategori::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT ref_jenis_sub_kategori OFF');
        Schema::enableForeignKeyConstraints();
    }
}
