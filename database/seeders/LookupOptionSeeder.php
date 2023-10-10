<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LookupOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        DB::unprepared('SET IDENTITY_INSERT lookup_options ON');
        //DB::table('lookup_options')->truncate();
        $json = file_get_contents(storage_path('/data/lookup_options.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['lookup_options'] as $datum) {
            \App\Models\LookupOption::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT lookup_options OFF');
        Schema::enableForeignKeyConstraints();

        // Schema::disableForeignKeyConstraints();
        // foreach ($this->master() as $datum) {
        //     //foreach ($this->data() as $datum) {
        //     $master = \App\Models\LookupOption::updateOrCreate([
        //         'uuid' => \Illuminate\Support\Str::uuid(),
        //         'key' => $datum['key'],
        //         'value' => $datum['value'],
        //         'json_value' => $datum['json'],
        //         'code' => $datum['code'],
        //         'dikemaskini_oleh' => $datum['dikemaskini_oleh'],
        //         'order_by' => $datum['order_by'],
        //     ]);
        // }
        // Schema::enableForeignKeyConstraints();
    }

    public function master()
    {
        return [           
            [
                'key' => 'butiran',
                'value' => 'Peningkatan Inovasi dan Kemajuan Teknologi',
                'code' => '13200',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'butiran',
                'value' => 'Pembangunan Sumber Air Negara',
                'code' => '13400',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'butiran',
                'value' => 'Pemulihan Empangan',
                'code' => '13500',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'butiran',
                'value' => 'Jentera-jentera dan Kelengkapan',
                'code' => '13600',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 4,
            ],
            [
                'key' => 'butiran',
                'value' => 'Tanggungan-tanggungan untuk Pengambilan Tanah',
                'code' => '13700',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 5,
            ],
            [
                'key' => 'butiran',
                'value' => 'Rancangan Kawalan dan Isyarat Bahaya Banjir',
                'code' => '13900',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 6,
            ],
            [
                'key' => 'butiran',
                'value' => 'Bangunan dan Pejabat JPS',
                'code' => '14100',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 7,
            ],
            [
                'key' => 'butiran',
                'value' => 'Pemulihan Struktur Rasional/Justifikasi Keperluan Projek',
                'code' => '14200',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 8,
            ],
            [
                'key' => 'butiran',
                'value' => 'Menaiktaraf Infrastruktur dan Saliran Bandar,Tebatan Banjir',
                'code' => '14500',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 9,
            ],
            [
                'key' => 'butiran',
                'value' => 'Kerja-kerja kecil JPS,Pelbagai Negeri',
                'code' => '15000',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 10,
            ],
            [
                'key' => 'butiran',
                'value' => 'Mencegah Hakisan Pantai',
                'code' => '15100',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 11,
            ],
            [
                'key' => 'butiran',
                'value' => 'Memperbaiki,Mengindah,Membersih dan Merawat Air Sungai-sungai dan Infrastruktur MASMA',
                'code' => '15200',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 12,
            ],
            [
                'key' => 'butiran',
                'value' => 'Mengorek Kuala-kuala Sungai',
                'code' => '15300',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 13,
            ],
            [
                'key' => 'butiran',
                'value' => 'Rancangan Pengurusan Sungai Saliran Mesra Alam',
                'code' => '15400',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 14,
            ],
            [
                'key' => 'butiran',
                'value' => 'Rancangan Tebatan Banjir (RTB) dan Saliran Bandar',
                'code' => '16700',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 15,
            ],
            [
                'key' => 'skop_project',
                'value' => 'Kerje Ukur',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'skop_project',
                'value' => 'Penyiasatan Tapak(S.I)',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'skop_project',
                'value' => 'pelantikan Perunding',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'jenis_kajian',
                'value' => 'Integrated River Basin Management(IRBM)',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'jenis_kajian',
                'value' => 'National Coastal Erosion Study(NCES)',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'jenis_kajian',
                'value' => 'lain-lain',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'kategory_hakisan',
                'value' => 'Kategori 1 - Critical',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'kategory_hakisan',
                'value' => 'Kategori 2 - Significant' ,
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'kategory_hakisan',
                'value' => 'Kategori 3 - Acceptable',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'kajian_kemungkinan',
                'value' => 'Pelan Induk Saliran Mesra Alama(PISMA)',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'kajian_kemungkinan',
                'value' => 'Integrated Shoreline Management Plan(ISMP)',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'kajian_kemungkinan',
                'value' => 'lain-lain',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'status_tab1',
                'value' => 'siap',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'status_tab1',
                'value' => 'Dalam Pembinaan',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'banjir_limpahan',
                'value' => '1 kali setahun',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'banjir_limpahan',
                'value' => '2-4 kali setahun',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'banjir_limpahan',
                'value' => '> 5 kali setahun',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'koridor_pembangunan',
                'value' => 'Wilayah Pembangunan Iskandar (WPI)',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'koridor_pembangunan',
                'value' => 'Northern Corridor Economic Region (NCER)',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'koridor_pembangunan',
                'value' => 'East Coast Economic Region (ECER)',
                'code' => '3',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'koridor_pembangunan',
                'value' => 'Sabah Development Corridor (SDC)',
                'code' => '4',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 4,
            ],
            [
                'key' => 'koridor_pembangunan',
                'value' => 'Sarawak Corridor of Renewable Energy (SCRE)',
                'code' => '5',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 5,
            ],
            [
                'key' => 'kategori_project',
                'value' => 'BAHARU',
                'code' => '1',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
            ],
            [
                'key' => 'kategori_project',
                'value' => 'SAMBUNGAN',
                'code' => '2',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
        ];
    }
}
