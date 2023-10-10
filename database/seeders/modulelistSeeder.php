<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\Pentadbir_modules;
use Illuminate\Support\Facades\DB;


class modulelistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pentadbir_modules')->delete();

        // Pentadbir_modules::truncate();

        $created_on = array(
            array(
                "modul_name" => "Pentadbir",
            ),
            array(
                "modul_name" => "Permohonan Projek",
            ),
            array(
                
                "modul_name" => "Pemantauan dan Penilaian Projek",
            ),
            array(
                
                "modul_name" => "Kontrak",
            ),
            array(
                
                "modul_name" => "Perunding",
            ),
            array(
                
                "modul_name" => "Value Management",
            ),  
            array(    
                "modul_name" => "Notice Of Change",
            ),          
            array(   
                "modul_name" => "Permohonan Peruntukan di luar rolling plan (RP)",
            ),
            array(
                "modul_name" => "Penjanaan Laporan",
            )
        );
// var_dump(count($created_on));
// exit();


        for($i=0;$i<count($created_on);$i++)
        {
        $deisgnation= new Pentadbir_modules;
        $deisgnation->modul_name=$created_on[$i]['modul_name'];
        $deisgnation->save();
        }
    }
}
