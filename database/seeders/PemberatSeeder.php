<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PemberatSeeder extends Seeder
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
        foreach ($this->master() as $datum) {
            //foreach ($this->data() as $datum) {
            $master = \App\Models\PemberatProjeckBaharu::updateOrCreate([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => $datum['name'],
                'description' => $datum['description'],
                'json_values' => $datum['json_values'],
                'type' => $datum['type'],                
                'pemberat' => $datum['pemberat'],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }

    public function master()
    {
        return [
            [
                'name' => 'kajian',
                'description' => 'kajian details',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 5}',                
                'pemberat' => 15,
            ],
            [
                'name' => 'pelan_induk',
                'description' => 'pelan induck details',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 5}',                
                'pemberat' => 15,
            ],
            [
                'name' => 'reka_bentuk',
                'description' => 'reka bentuk details',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"dalam_penyediaan_reka_bentuk" : 3, "reka_siap" : 5}',
                'pemberat' => 75,
            ],
            [
                'name' => 'whishlist',
                'description' => 'whishlist kerajaan Negeri',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0, "ada" : 5}',
                'pemberat' => 25,
            ],
            [
                'name' => 'upen',
                'description' => 'sokongan dari UPEN',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 1}',                
                'pemberat' => 25,
            ],
            [
                'name' => 'upen1',
                'description' => 'sokongan dari UPEN',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 5}',
                'pemberat' => 25,
            ],
            [
                'name' => 'kelulusan',
                'description' => 'kelulusan daripada MKM/MTPNg',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 1}',                
                'pemberat' => 25,
            ],
            [
                'name' => 'banjir',
                'description' => 'kekerapan banjir Sungai/Pantai',
                'type' => 'dropdown',
                'json_values' => '{"option1" : 0,"option2" : 3,"option3" : 5}',                
                'pemberat' => 50,
            ],
            [
                'name' => 'projek_berfasa',
                'description' => 'Projeck Berfasa',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 5}',
                'pemberat' => 50,
            ],
            [
                'name' => 'dibahasa',
                'description' => 'Dibahas dalam Parlimen/Permohonan Khas (ADUN/Ahli Parlimen/Persatuan/Pertubuhan dll)',
                'type' => 'radio',
                'json_values' => '{"tiada" : 0,"ada" : 5}',                
                'pemberat' => 50,
            ]
            ];
    }
}

