<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('lookups')->truncate();
        foreach ($this->master() as $datum) {
            //foreach ($this->data() as $datum) {
            $master = \App\Models\Lookup::updateOrCreate([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'key' => $datum['key'],
                'value' => $datum['value'],
                'json_value' => $datum['json'],
                'code' => $datum['code'],
                'dikemaskini_oleh' => $datum['dikemaskini_oleh'],
                'order_by' => $datum['order_by'],
            ]);
        }
        Schema::enableForeignKeyConstraints();
    }

    public function master()
    {
        return [
            [
                'key' => 'master',
                'value' => 'Negeri',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 1,
                
            ],
            [
                'key' => 'master',
                'value' => 'Daerah',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 2,
            ],
            [
                'key' => 'master',
                'value' => 'Parlimen',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 3,
            ],
            [
                'key' => 'master',
                'value' => 'Dun',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 4,
            ],
            [
                'key' => 'master',
                'value' => 'Kementerian',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 5,
            ],
            [
                'key' => 'master',
                'value' => 'Jabatan',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 6,
            ],
            [
                'key' => 'master',
                'value' => 'Bahagian',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 7,
            ],
            [
                'key' => 'master',
                'value' => 'Jawatan',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 8,
            ],
            [
                'key' => 'master',
                'value' => 'Gred',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 9,
            ],
            [
                'key' => 'master',
                'value' => 'Mukim',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 10,
            ],
            [
                'key' => 'master',
                'value' => 'Jenis Kategori / Jenis Kategori Perolehan',                
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 11,
            ],
            [
                'key' => 'master',
                'value' => 'Jenis Sub Kategori / Jenis Perolehan',                
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 12,
            ],
            [
                'key' => 'master',
                'value' => 'Bahagian EPU JPM',                
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 13,
            ],
            [
                'key' => 'master',
                'value' => 'Sektor Utama',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 14,
            ],
            [
                'key' => 'master',
                'value' => 'Sektor',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 15,
            ],
            [
                'key' => 'master',
                'value' => 'Sub Sektor',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 16,
            ],
            [
                'key' => 'master',                
                'value' => 'Unit',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 17,
            ],
            [
                'key' => 'master',                
                'value' => 'OBB',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 18,
            ],
            [
                'key' => 'master',                
                'value' => 'RMK',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 19,
            ],
            [
                'key' => 'master',                
                'value' => 'Komponen',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 20,
            ],
            [
                'key' => 'master',                
                'value' => 'Pejabat Projek',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 21,
            ],
            [
                'key' => 'master',                
                'value' => 'Role',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 22,
            ],
            [
                'key' => 'master',                
                'value' => 'User Types',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 23,
            ],
            [
                'key' => 'master',                
                'value' => 'Permissions',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 24,
            ],
            [
                'key' => 'master',                
                'value' => 'Module',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 25,
            ],
            [
                'key' => 'master',                
                'value' => 'Deliverable Headings',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 26,
            ],
            [
                'key' => 'master',                
                'value' => 'Deliverables',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 27,
            ],
            [
                'key' => 'master',                
                'value' => 'Belanja mengurus skop',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 28,
            ],
            [
                'key' => 'master',                
                'value' => 'Belanja mengurus subskop',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 29,
            ],
            [
                'key' => 'master',                
                'value' => 'Nama Agensi',
                'code' => '',
                'json' => '{}',
                'dikemaskini_oleh' => 1,
                'order_by' => 30,
            ],
        ];
    }

}
