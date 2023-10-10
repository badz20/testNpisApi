<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Status;
use Illuminate\Support\Facades\DB;


class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->delete();

        $created_on = array(
            array(
                "status" => 1,
                "status_name" => 'Dalam Penyediaan'
            ),
            array(
                "status" => 2,
                "status_name"=> 'Diserahkan oleh Penyemak'
            ),
            array(
                "status" => 3,
                "status_name"=> 'Sedang Disemak oleh Penyemak'
            ),
            array(
                "status" => 4,
                "status_name"=> 'Telah Disemak oleh Penyemak'
            ),
            array(
                "status" => 5,
                "status_name"=> 'Permintaan untuk Dikemaskini oleh Penyemak'
            ),
            array(
                "status" => 6,
                "status_name"=> 'Sedang Disemak oleh Penyemak 1'
            ), 
            array(
                "status" => 7,
                "status_name"=> 'Telah Disemak oleh Penyemak 1'
            ),         
            array(
                "status" => 8,
                "status_name"=> 'Permintaan untuk Dikemaskini oleh Penyemak 1'
            ),
            array(
                "status" => 9,
                "status_name"=> 'Ditolak oleh Penyemak 1'
            ),
            array(
                "status" => 10,
                "status_name"=> 'Sedang Disemak oleh Penyemak 2'
            ),
            array(
                "status" => 11,
                "status_name"=> 'Telah Disemak oleh Penyemak 2'
            ),
            array(
                "status" => 12,
                "status_name"=> 'Permintaan untuk Dikemaskini oleh Penyemak 2'
            ),
            array(
                "status" => 13,
                "status_name"=> 'Untuk Pengesahan Pengarah Bahagian'
            ),
            array(
                "status" => 14,
                "status_name"=> 'Disahkan oleh Pengesah'
            ),
            array(
                "status" => 15,
                "status_name"=> 'Permintaan untuk dikemaskini oleh Pengesah'
            ),
            array(
                "status" => 16,
                "status_name"=> 'Ditolak oleh Pengesah'
            ),
            array(
                "status" => 17,
                "status_name"=> 'Diluluskan Peraku'
            ),
            array(
                "status" => 18,
                "status_name"=> 'Permintaan untuk Dikemaskini oleh Peraku'
            ),
            array(
                "status" => 19,
                "status_name"=> 'Ditolak oleh Peraku'
            ),
            array(
                "status" => 20,
                "status_name"=> 'Dibatalkan'
            ),
            array(
                "status" => 21,
                "status_name"=> 'Belum laksana'
            ),
            array(
                "status" => 22,
                "status_name"=> 'Dalam perancangan'
            ),
            array(
                "status" => 23,
                "status_name"=> 'Pengecualian'
            ),
            array(
                "status" => 24,
                "status_name"=> 'Dalam pelaksanaan'
            ),
            array(
                "status" => 26,
                "status_name"=> 'Menunggu penyerahan Bahagian'
            ),
            array(
                "status" => 27,
                "status_name"=> 'Sedang disemak oleh BKOR'
            ),
            array(
                "status" => 28,
                "status_name"=> 'Perlu dikemaskini oleh Bahagian'
            ),
            array(
                "status" => 29,
                "status_name"=> 'Semakan pengemaskinian oleh BKOR'
            ),
            array(
                "status" => 30,
                "status_name"=> 'Semakan oleh BPU-EPU'
            ),
            array(
                "status" => 31,
                "status_name"=> 'Penyediaan Draf Laporan Makmal'
            ),
            array(
                "status" => 32,
                "status_name"=> 'Dalam Semakan'
            ),
            array(
                "status" => 33,
                "status_name"=> 'Perlu dikemaskini oleh Bahagian'
            ),
            array(
                "status" => 34,
                "status_name"=> 'Tandatangan Laporan'
            ),
            array(
                "status" => 35,
                "status_name"=> 'Dalam Penjilidan'
            ),
            array(
                "status" => 36,
                "status_name"=> 'Selesai'
            ),
            array(
                "status" => 40,
                "status_name"=> 'Daftar/Kemaskini Permohonan'
            ),
            array(
                "status" => 41,
                "status_name"=> 'Dalam Tindakan Kementerian'
            ),
            array(
                "status" => 42,
                "status_name"=> 'Dalam Tindakan Kementerian Ekonomi'
            ),
            array(
                "status" => 43,
                "status_name"=> 'Batal'
            ),
            array(
                "status" => 44,
                "status_name"=> 'Lulus'
            ),
            array(
                "status" => 45,
                "status_name"=> 'Tidak Lulus'
            ),
        );

        //var_dump($created_on);
// exit();


        for($i=0;$i<count($created_on);$i++)
        {
            $deisgnation= new Status;
            $deisgnation->status=$created_on[$i]["status"];
            $deisgnation->status_name=$created_on[$i]["status_name"];
            $deisgnation->save();
        }
    }
}
