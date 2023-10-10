<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $datum) {
            $jenisKategory = \App\Models\JenisKategori::updateOrCreate([
                'name' => $datum['name'],
                'kod_kategori' => $datum['kod_kategori'],
                'dibuat_oleh' => $datum['dibuat_oleh'],
            ]);

            if (count($datum['child']) > 0) {
                foreach ($datum['child'] as $child) {
                    $child['kategori_id'] = $jenisKategory->id;
                    \App\Models\JenisSubKategori::updateOrCreate($child);
                }
            }
        }
    }

    private function data()
    {
        return [
            [
                'name' => 'Fizikal - Pemninaan',
                'kod_kategori' => 'K1',
                'dibuat_oleh' => 1,
                'child' => [
                    ['name' => 'Pembinaan Baharu','dibuat_oleh' => 1,'kod_sub_kategori' => 'K11',],
                    ['name' => 'Ubahsuai','dibuat_oleh' => 1,'kod_sub_kategori' => 'K12',],
                    ['name' => 'Tidak Berkenaan','dibuat_oleh' => 1,'kod_sub_kategori' => 'K13',],
                ],
            ],
            [
                'name' => 'Fizikal - Penyengaraan',
                'kod_kategori' => 'K2',
                'dibuat_oleh' => 1,
                'child' => [
                    ['name' => 'Pembinaan Baharu','dibuat_oleh' => 1,'kod_sub_kategori' => 'K21',],
                    ['name' => 'Ubahsuai','dibuat_oleh' => 1,'kod_sub_kategori' => 'K22',],
                    ['name' => 'Tidak Berkenaan','dibuat_oleh' => 1,'kod_sub_kategori' => 'K23',],
                ],
            ],
            [
                'name' => 'Fizikal - Kelengakapan/Peralatan',
                'kod_kategori' => 'K3',
                'dibuat_oleh' => 1,
                'child' => [
                    ['name' => 'Pembinaan Baharu','dibuat_oleh' => 1,'kod_sub_kategori' => 'K31',],
                    ['name' => 'Ubahsuai','dibuat_oleh' => 1,'kod_sub_kategori' => 'K32',],
                    ['name' => 'Tidak Berkenaan','dibuat_oleh' => 1,'kod_sub_kategori' => 'K33',],
                ],
            ],
            [
                'name' => 'Bukan Fizikal - ICT',
                'kod_kategori' => 'K4',
                'dibuat_oleh' => 1,
                'child' => [
                    ['name' => 'Pembinaan Baharu','dibuat_oleh' => 1,'kod_sub_kategori' => 'K41',],
                    ['name' => 'Ubahsuai','dibuat_oleh' => 1,'kod_sub_kategori' => 'K42',],
                    ['name' => 'Tidak Berkenaan','dibuat_oleh' => 1,'kod_sub_kategori' => 'K43',],
                ],
            ],
            [
                'name' => 'Bukan Fizikal - Kajian',
                'kod_kategori' => 'K5',
                'dibuat_oleh' => 1,
                'child' => [
                    ['name' => 'Pembinaan Baharu','dibuat_oleh' => 1,'kod_sub_kategori' => 'K51',],
                    ['name' => 'Ubahsuai','dibuat_oleh' => 1,'kod_sub_kategori' => 'K52',],
                    ['name' => 'Tidak Berkenaan','dibuat_oleh' => 1,'kod_sub_kategori' => 'K53',],
                ],
            ],
        ];
    }
}
