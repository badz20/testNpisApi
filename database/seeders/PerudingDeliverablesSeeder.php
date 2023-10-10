<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PerudingDeliverablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Schema::disableForeignKeyConstraints();
        // DB::unprepared('SET IDENTITY_INSERT lookup_options ON');
        // //DB::table('lookup_options')->truncate();
        // $json = file_get_contents(storage_path('/data/lookup_options.json'));
        // $json_data = json_decode($json,true);
        
        // foreach ($json_data['lookup_options'] as $datum) {
        //     \App\Models\LookupOption::create($datum);
        // }
        // DB::unprepared('SET IDENTITY_INSERT lookup_options OFF');
        // Schema::enableForeignKeyConstraints();

        // Schema::disableForeignKeyConstraints();
        foreach ($this->master() as $datum) {
            //foreach ($this->data() as $datum) {
            $master = \App\Models\LookupOption::updateOrCreate([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'key' => $datum['key'],
                'value' => $datum['value'],
                'json_value' => $datum['json'],
                'code' => $datum['code'],
                'row_status' => $datum['row_status'],
                'is_hidden' => $datum['is_hidden'],
                'dibuat_oleh' => $datum['dibuat_oleh'],
                'dikemaskini_oleh' => $datum['dikemaskini_oleh'],
                'order_by' => $datum['order_by'],
            ]);
        }
        // Schema::enableForeignKeyConstraints();
    }

    public function master()
        {
            return [           
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'PERUNDING ARKITEK/PERUNDING M&E',
                    'code' => '',
                    'json' => '{"is_heading": 1}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 1,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Inception Report',
                    'code' => 'A1',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 2,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Preliminary Review Design Report',
                    'code' => 'A2',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 3,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Conceptual Design Report',
                    'code' => 'A3',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 3,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Interim Design Report',
                    'code' => 'A4',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 4,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Draft Final Detail Desgin Report',
                    'code' => 'A5',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 5,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Final Details Desig Report',
                    'code' => 'A6',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 6,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Tender Document and Drawing',
                    'code' => 'A7',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 7,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Specification and O & M Drawing',
                    'code' => 'A8',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 8,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Tender Stage',
                    'code' => 'A9',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 9,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Kerje-Kerje Pengawasan/Home office Support',
                    'code' => 'A10',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 10,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Construction Stage',
                    'code' => 'A11',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 11,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Defect Liabiliti Period',
                    'code' => 'A12',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 12,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Bayaran Akhir',
                    'code' => 'A12',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING ARKITEK/PERUNDING M&E"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 12,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'PERUNDING UKUR',
                    'code' => '',
                    'json' => '{"is_heading": 1}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 13,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Pelan Kerja Ukur',
                    'code' => 'B1',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 14,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Pelan Pengambilan Tanah',
                    'code' => 'B2',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 15,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Penandaan Sempadan',
                    'code' => 'B3',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 16,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Penandaan Rizab - Pewartaan',
                    'code' => 'B4',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 17,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Penandaan Rizab - Pengukuran Halus',
                    'code' => 'B5',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 18,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Bayar Akhir',
                    'code' => 'B6',
                    'json' => '{"is_heading": 0,"heading_name":"PERUNDING UKUR"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 19,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'KAJIAN',
                    'code' => '',
                    'json' => '{"is_heading": 1}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 20,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Inception Report',
                    'code' => 'C1',
                    'json' => '{"is_heading": 0,"heading_name":"KAJIAN"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 21,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Interim Report',
                    'code' => 'C2',
                    'json' => '{"is_heading": 0,"heading_name":"KAJIAN"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 22,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Draft Final Report',
                    'code' => 'C3',
                    'json' => '{"is_heading": 0,"heading_name":"KAJIAN"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 23,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Final Report',
                    'code' => 'C4',
                    'json' => '{"is_heading": 0,"heading_name":"KAJIAN"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 24,
                ],
                [
                    'key' => 'perunding_deliverable',
                    'value' => 'Bayaran Akhir',
                    'code' => 'C5',
                    'json' => '{"is_heading": 0,"heading_name":"KAJIAN"}',
                    'row_status' => 1,
                    'is_hidden' => 1,
                    'dibuat_oleh' => 1,
                    'dikemaskini_oleh' => 1,
                    'order_by' => 25,
                ]
            ];
        }
}