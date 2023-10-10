<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class KemeneterianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->master() as $datum) {
            //foreach ($this->data() as $datum) {
                //dd($datum['jabatan']);
            $kementerian = \App\Models\refKementerian::updateOrCreate([
                //'uuid' => \Illuminate\Support\Str::uuid(),
                'kod_kementerian' => $datum['kod_kementerian'],
                'nama_kementerian' => $datum['nama_kementerian'],
                'penerangan_kementerian' => $datum['penerangan_kementerian'],
                'dibuat_oleh' => $datum['dibuat_oleh'],
                'is_hidden' => 0,
                'row_status' => 1,
            ]);
                
            if (count($datum['jabatan']) > 0) {
                
                foreach ($datum['jabatan'] as $jabatanchild) {                    
                    $jabtan1['kementerian_id'] = $kementerian->id;
                    $jabtan1['kod_jabatan'] = $jabatanchild['kod_jabatan'];
                    $jabtan1['nama_jabatan'] = $jabatanchild['nama_jabatan'];
                    $jabtan1['penerangan_jabatan'] = $jabatanchild['penerangan_jabatan'];
                    $jabtan1['dibuat_oleh'] = $jabatanchild['dibuat_oleh'];
                    $jabtan1['is_hidden'] = 0;
                    $jabtan1['row_status'] = 1;
                    
                    $jabatan = \App\Models\refJabatan::updateOrCreate($jabtan1);
                    
                    if (count($jabatanchild['bahagian']) > 0) {
                        foreach ($jabatanchild['bahagian'] as $bahagianchild) {
                            $bahagian['kementerian_id'] = $kementerian->id;
                            $bahagian['jabatan_id'] = $jabatan->id;
                            $bahagian['kod_bahagian'] = $bahagianchild['kod_bahagian'];
                            $bahagian['nama_bahagian'] = $bahagianchild['nama_bahagian'];
                            $bahagian['penerangan_bahagian'] = $bahagianchild['penerangan_bahagian'];
                            $bahagian['dibuat_oleh'] = $bahagianchild['dibuat_oleh'];
                            $bahagian['row_status'] = 1;
                            $bahagian['is_hidden'] = 0;
                            \App\Models\refBahagian::updateOrCreate($bahagian);
                            
                        }
                    }
                    
                }
            }
        }
        Schema::enableForeignKeyConstraints();
    }

    public function master()
    {
        return [
            [
                'kod_kementerian' => 'K01',
                'nama_kementerian' => 'Kementerian Sumber Asli, Alam Sekitar dan Perubahan Iklim (NRECC)',
                'penerangan_kementerian' => '',
                'dibuat_oleh' => 1,
                'jabatan' => [
                    [
                        'kod_jabatan' => 'J01',
                        'nama_jabatan' => 'Jabatan1',
                        'penerangan_jabatan' => '',
                        'dibuat_oleh' => 1,
                        'bahagian' => [
                            [
                                'kod_bahagian' => '1',
                                'nama_bahagian' => 'Bahagian Pengurusan Zon Pantai (BPZP)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '2',
                                'nama_bahagian' => 'Bahagian Pengurusan Sumber Air & Hidrologi (BPSAH)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '3',
                                'nama_bahagian' => 'Bahagian Pengurusan Lembangan Sungai (BPLS)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '4',
                                'nama_bahagian' => 'Bahagian Rekabentuk & Empangan (BRE)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '5',
                                'nama_bahagian' => 'Pusat Ramalan & Amaran Banjir Negara (PRABN)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '6',
                                'nama_bahagian' => 'Bahagian Pengurusan Banjir (BPB)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '7',
                                'nama_bahagian' => 'Bahagian Saliran Mesra Alam (BSMA)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '8',
                                'nama_bahagian' => 'Humid Tropics Centre KL (HTC KL)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '9',
                                'nama_bahagian' => 'Bahagian Pengurusan Fasiliti & GIS (BPFG)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '10',
                                'nama_bahagian' => 'Bahagian Korporat (BKOR)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ],
                            [
                                'kod_bahagian' => '11',
                                'nama_bahagian' => 'Bahagian Ukur Bahan & Pengurusan Kontrak (BUBPK)',
                                'penerangan_bahagian' => '',
                                'dibuat_oleh' => 1,
                            ]

                        ]
                    ],
                    [
                        'kod_jabatan' => 'J02',
                        'nama_jabatan' => 'Jabatan2',
                        'penerangan_jabatan' => '',
                        'dibuat_oleh' => 1,
                        'bahagian' => []
                    ],
                    [
                        'kod_jabatan' => 'J03',
                        'nama_jabatan' => 'Jabatan3',
                        'penerangan_jabatan' => '',
                        'dibuat_oleh' => 1,
                        'bahagian' => []
                    ],
                ]

            ],
            [
                'kod_kementerian' => 'K02',
                'nama_kementerian' => 'Kementerian Tenaga dan Sumber Asli (KeTSA)',
                'penerangan_kementerian' => '',
                'dibuat_oleh' => 1,
                'jabatan' => []
            ],
        ];
    }
}
