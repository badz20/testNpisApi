<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DevSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\refJenisPengguna::create([
            'kod_jenis_pengguna' => '1',
            'nama_jenis_pengguna' => 'Pengguna JPS',
            'penerangan_jenis_pengguna' => 'Pengguna JPS',
            'dibuat_oleh' => 1,
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_oleh' => 1,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_hidden' => 0,
            'row_status' => 1,
        ]);

        \App\Models\refJenisPengguna::create([
            'kod_jenis_pengguna' => '2',
            'nama_jenis_pengguna' => 'Agensi Luar',
            'penerangan_jenis_pengguna' => 'Agensi Luar',
            'dibuat_oleh' => 1,
            'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'dikemaskini_oleh' => 1,
            'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_hidden' => 0,
            'row_status' => 1,
        ]);

        // \App\Models\refKementerian::create([
        //     'kod_kementerian' => '1',
        //     'nama_kementerian' => 'Kemeneterian1',
        //     'penerangan_kementerian' => 'test description',
        //     'dibuat_oleh' => 1,            
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);     
        
        // \App\Models\refJabatan::create([
        //     'kod_jabatan' => '1',
        //     'nama_jabatan' => 'Jabatan1',
        //     'penerangan_jabatan' => 'test description',
        //     'kementerian_id' => 1,
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refBahagian::create([
        //     'kod_bahagian' => '0',
        //     'nama_bahagian' => 'Bahagian1',
        //     'penerangan_bahagian' => 'test description',
        //     'dibuat_oleh' => 1,
        //     'kementerian_id' => 1,
        //     'jabatan_id' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);        

        // \App\Models\refGredJawatan::create([
        //     'kod_gred' => '1',
        //     'nama_gred' => 'Gred1',
        //     'penerangan_gred' => 'test description',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        

        // \App\Models\refJawatan::create([
        //     'kod_jawatan' => '1',
        //     'nama_jawatan' => 'Jawantan1',
        //     'penerangan_jawatan' => 'test description',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refJenisPengguna::create([
        //     'kod_jenis_pengguna' => '1',
        //     'nama_jenis_pengguna' => 'Pengguna JPS',
        //     'penerangan_jenis_pengguna' => 'Pengguna JPS',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refJenisPengguna::create([
        //     'kod_jenis_pengguna' => '2',
        //     'nama_jenis_pengguna' => 'Agensi Luar',
        //     'penerangan_jenis_pengguna' => 'Agensi Luar',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refNegeri::create([
        //     'kod_negeri' => '1',
        //     'nama_negeri' => 'Selangor',
        //     'penerangan_negeri' => 'test description',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refNegeri::create([
        //     'kod_negeri' => '2',
        //     'nama_negeri' => 'Kuala Lumpur',
        //     'penerangan_negeri' => 'test description',
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);


        // \App\Models\refDaerah::create([
        //     'kod_daerah' => '1',
        //     'kod_negeri' => '1',
        //     'nama_daerah' => 'Ampang',
        //     'penerangan_daerah' => 'test description',
        //     'negeri_id' => 1,
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\refMukim::create([
        //     'kod_mukim' => '1',
        //     'nama_mukim' => 'bukit indah',
        //     'penerangan_mukim' => 'test description',
        //     'negeri_id' => 1,
        //     'daerah_id' => 1,
        //     'dibuat_oleh' => 1,
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_oleh' => 1,
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'is_hidden' => 0,
        //     'row_status' => 1,
        // ]);

        // \App\Models\User::create([
        //     'name' => 'superadmin',
        //     'email' => 'superadmin@app.com',
        //     'password' => Hash::make('password'),
        //     'no_ic' => '1234567',
        //     'jenis_pengguna_id' => 1,
        //     'no_telefon' => '23423423',
        //     'jawatan_id' => 1,
        //     'jabatan_id' => 1,
        //     'gred_jawatan_id' => 1,
        //     //'kementerian' => $data['kementerian'],
        //     'bahagian_id' => 1,
        //     'negeri_id' => 1,
        //     'daerah_id' => 1,
        //     'catatan' => 'descriptiopj',
        //     'dibuat_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'dikemaskini_pada' => Carbon::now()->format('Y-m-d H:i:s'),
        // ]);

        $this->call([
            
            refNegeri::class,
            refDaerah::class,
            refMukim::class,
            refKementerian::class,
            refJabatan::class,
            refBahagian::class,
            refParlimen::class,
            refDun::class,
            refGred::class,
            refJawatan::class,                        
            LookupSeeder::class,
            LookupOptionSeeder::class,
            //RollingPlanSeeder::class,
            //JenisKategoriSeeder::class,
            BahagianEpuSeeder::class,
            //KemeneterianSeeder::class,
            modulelistSeeder::class,
            PemberatSeeder::class,
            PejabatSeeder::class,
            JenisKategory::class,
            JenisSubKategory::class,
            ObbActivity::class,
            RMKSDG::class,
            Sasaran::class,
            SektorUtama::class,
            Sektor::class,            
            Stretagi::class,
            SubSektor::class,
            refIndikator::class,
            SkopSeeder::class,
            SubSkopSeeder::class,
            refKomponen::class,
            refUnit::class,
            RollingPlans::class,
            PejabatSeeder::class,
            MasterPerananSeeder::class,
            UserSeeder::class,
            tempUserSeeder::class,
            ModuleLinkMasterSeeder::class,
            BriefProjekSeeder::class,
            UserTypesSeeder::class,
            RoleSeeder::class
        ]);
    }
}
